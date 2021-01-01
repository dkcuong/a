var TICKET_PRINTER = {
	printer_address : '',
	printer_port: 0,
	printer: null,
	ePosDev: null,
	instance: null,
	initialize: async function(retry) {
		// Declare the ePos Device 
		TICKET_PRINTER.ePosDev = new epson.ePOSDevice();

		// set to instance for easier to use in following actions
		this.instance = TICKET_PRINTER.ePosDev;

		try {
			// trigger to Connect the device, await the result to continue
			await this.callback_connect();

			// if the result of connect is positive, continue to Create Device
			// await the result to continue, set the this.printer
			this.printer = await this.callback_create_device();

			return new Promise((resolve, reject) => {
				// if this.printer is not empty, complete and return the this.printer object
				if( this.printer ) {
					// retry = 3;
					resolve( this.printer );
				} else {
					reject();
				}
			});
		} catch (err) {
			return new Promise((resolve, reject) => {
				console.log( "err :" );
				console.log( err );
				
				// Catch all errors from above callback functions
				if( err == "ERROR_TIMEOUT" ) {
					if( retry > 0 ){

						setTimeout(() => {
							retry--;
							this.initialize(retry).then((result) => {
								if( this.printer ) {
									resolve( this.printer );
								} else {
									reject();
								}
							}, (result) => {
								reject();
							});
						}, 3000);
					} else {
						reject();
					}
				}
			}).catch(function( err ){
				console.log("Failed to connect the Printer");
			});
		}
	},
	callback_connect: function() {
		return new Promise((resolve, reject) => {
			// Try to connect to the device
			//this.instance.connect('192.168.1.111', 8043, data => {
			this.instance.connect(TICKET_PRINTER.printer_address, TICKET_PRINTER.printer_port, data => {
				if(data == 'OK' || data == 'SSL_CONNECT_OK') {
					// if connection OK, resolve and return
					resolve();
				} else {
					// if connection failed, reject and return
					reject( data );
				}
			})
		});
	},
	callback_create_device: function(){
		return new Promise((resolve, reject) => {
			// Try to create the Printer device
			this.instance.createDevice(
				'local_printer',
				this.instance.DEVICE_TYPE_PRINTER,
				{'cryto': true, 'buffer': true},
				(devobj, retcode) => {
					if(retcode == 'OK') {
						// if create the Printer device successfully, configure the Printer and its buffer
						this.printer = devobj;
						this.printer.timeout = 600000;

						resolve(devobj);
					} else {
						// if create the Printer device failed, reject and return
						reject(retcode);
					}
				},
			);
		});
	},
	kickDrawer: function (){
		console.log("kickDrawer start");

		// kick out the drawer
		this.printer.addPulse(this.printer.DRAWER_1, this.printer.PULSE_500);

		this.printer.send();

		console.log("kickDrawer end");
	},
	printTicket: function( data ){
		console.log("printTicket start");

		this.printer.addPageBegin();

		// Set some common settings for the whole page
		// this.printer.addTextSmooth(true);
		this.printer.addTextLang("zh-hant");
		this.printer.addTextFont(this.printer.FONT_A);

		/**
		 * Create 2 "Page area" for printing
		 */
		{
			// left page area
			
			// Set the default line height in terms of "dot" --> 1 mm = 7.2 dots
			this.printer.addTextLineSpace(38); // set the line height to 38 dots
			
			// Specify the size and position of the print area
			// for this setting, assuming there are 10 lines, each line height is 38 dots (around 5.3mm)
			this.printer.addPageArea(0, 86, 324, 380);
			
			// row 1
			this.printer.addTextPosition(0);          // set the X position of the print text, in term of dot
			this.printer.addTextVPosition(38);            // set the Y position of the print text, in term of dot
			this.printer.addTextDouble(false, true);  // set the Double height of the print text, 1st parameter = double width, 2nd = double height
			this.printer.addText(data.name);     // add the print text
			this.printer.addTextDouble(false, false);     // reset the Double width / height of the print text
			
			// row 2
			
			// row 3
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(76);
			this.printer.addText("Price:");
			this.printer.addTextPosition(180);
			this.printer.addTextVPosition(76);
			this.printer.addText("Category:");
			
			// row 4
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(114);
			this.printer.addText("$" + data.price);
			this.printer.addTextPosition(180);
			this.printer.addTextVPosition(114);
			this.printer.addText(data.category);
			
			// row 5
			this.printer.addTextDouble(false, true);
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(152);
			this.printer.addText(data.schedule_date);
			this.printer.addTextPosition(180);
			this.printer.addTextVPosition(165);
			this.printer.addTextSize(2, 2);
			this.printer.addText(data.schedule_time);
			this.printer.addTextSize(1, 1);
			this.printer.addTextDouble(false, false);
			
			// row 6
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(190);
			this.printer.addText(data.schedule_year);
			
			// row 7
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(228);
			this.printer.addText("House:");
			this.printer.addTextPosition(180);
			this.printer.addTextVPosition(228);
			this.printer.addText("Seat:");
			
			// row 8
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(266);
			this.printer.addTextSize(2, 2);
			this.printer.addText(data.schedule_house);
			this.printer.addTextSize(1, 1);
			this.printer.addTextPosition(180);
			this.printer.addTextVPosition(266);
			this.printer.addTextSize(2, 2);
			this.printer.addText(data.schedule_seat);
			this.printer.addTextSize(1, 1);
			
			// row 9
			
			// row 10
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(342);
			this.printer.addText(data.payment_reference);
			
			if( data.reprint == true ){
				this.printer.addTextPosition(240);
				this.printer.addTextVPosition(342);
				this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
				this.printer.addText("REPRINT");
			}
		}

		{
			// right page area
			this.printer.addTextLineSpace(38); // set the line height to 38 dots
			
			// Specify the size and position of the print area
			this.printer.addPageArea(370, 90, 144, 365);
			
			// row 1
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(38);
			this.printer.addTextDouble(false, true);
			this.printer.addText(data.name);
			this.printer.addTextDouble(false, false);
			
			// row 2
			
			// row 3
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(90);
			this.printer.addTextAlign(this.printer.ALIGN_LEFT);
			this.printer.addText("$" + data.price);
			this.printer.addTextPosition(72);
			this.printer.addTextVPosition(90);
			this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
			this.printer.addText(data.category);
			
			// row 4
			this.printer.addTextDouble(false, true);
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(135);
			this.printer.addText(data.schedule_date + ", " +data.schedule_year);
			this.printer.addTextDouble(false, false);
			
			// row 5
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(180);
			this.printer.addTextSize(2, 2);
			this.printer.addText(data.schedule_time);
			this.printer.addTextSize(1, 1);
			this.printer.addTextDouble(false, false);
			
			// row 6
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(220);
			this.printer.addText("House: ");
				this.printer.addTextSize(2, 2);
				this.printer.addText(data.schedule_house);
				this.printer.addTextSize(1, 1);
			
			// row 7
			this.printer.addTextPosition(0);
			this.printer.addTextVPosition(260);
			this.printer.addText("Seat : ");
				this.printer.addTextSize(2, 2);
				this.printer.addText(data.schedule_seat);
				this.printer.addTextSize(1, 1);
			
			// row 8
			// this.printer.addTextPosition(0);
			// this.printer.addTextVPosition(266);
			// this.printer.addText(data.payment_reference);
			
			// row 9

			// row 10
			if( data.reprint == true ){
				this.printer.addTextPosition(72);
				this.printer.addTextVPosition(330);
				this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
				this.printer.addText("REPRINT");
			}
		}

		// End the "Page mode"
		this.printer.addPageEnd();	

		// Set the Feed Position to "Cutting edge" -- the Black marker at the back of ticket
		this.printer.addFeedPosition(this.printer.FEED_CUTTING);

		// Set to Cut the paper without feed 
		this.printer.addCut(this.printer.CUT_NO_FEED);

		// Send all set content in the buffer to the Print, trigger to "Print" the real ticket
		this.printer.send();

		console.log("printTicket end");
	},
	printTicketReceipt: function( data ){
		console.log("printTicketReceipt start");

		this.printer.addPageBegin();

		// Set some common settings for the whole page
		// this.printer.addTextSmooth(true);
		this.printer.addTextLang("zh-hant");
		this.printer.addTextFont(this.printer.FONT_A);

		// Set the default line height in terms of "dot" --> 1 mm = 7.2 dots
		this.printer.addTextLineSpace(38); // set the line height to 38 dots

		// Specify the size and position of the print area
		// for this setting, assuming there are 10 lines, each line height is 38 dots (around 5.3mm)
		this.printer.addPageArea(0, 86, 504, 380);

		// row 1
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(38);						// set the Y position of the print text, in term of dot
		this.printer.addTextAlign(this.printer.ALIGN_CENTER);
		this.printer.addTextDouble(false, true); 				// set the Double height of the print text, 1st parameter = double width, 2nd = double height
		this.printer.addText("正式收據 Official Receipt");			// add the print text
		this.printer.addTextDouble(false, false); 				// reset the Double width / height of the print text
		this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 2
		
		// row 3
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(76);
		this.printer.addText("編號 No.");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(76);
		this.printer.addText( ": " + data.receipt_number );

		// row 4
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(114);
		this.printer.addText("戲院 Cinema");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(114);
		this.printer.addText( ": " + data.cinema );

		// row 5
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(152);
		this.printer.addText("電影 Movie");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(152);
		this.printer.addText( ": " + data.movie );

		// row 6
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(190);
		this.printer.addText("時間 ShowTime");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(190);
		this.printer.addText( ": " + data.showtime );

		// row 7
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(228);
		this.printer.addText("座位 Seat");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(228);
		this.printer.addText( ": " + data.house_seats );

		// row 8
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(266);
		this.printer.addText("方法 Payment");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(266);
		this.printer.addText( ": " + data.payment_method );

		// row 9
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(304);
		this.printer.addText("總額 Amount");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(304);
		this.printer.addText( ": " + data.amount );

		// row 10
		this.printer.addTextPosition(0);
		this.printer.addTextVPosition(342);
		this.printer.addText("職員 Staff");

		this.printer.addTextPosition(144);
		this.printer.addTextVPosition(342);
		this.printer.addText( ": " + data.staff );

		if( data.reprint == true ){
			this.printer.addTextPosition(240);
			this.printer.addTextVPosition(342);
			this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
			this.printer.addText("REPRINT");
		}

		// End the "Page mode"
		this.printer.addPageEnd();	

		// Set the Feed Position to "Cutting edge" -- the Black marker at the back of ticket
		this.printer.addFeedPosition(this.printer.FEED_CUTTING);

		// Set to Cut the paper without feed 
		this.printer.addCut(this.printer.CUT_NO_FEED);

		// Send all set content in the buffer to the Print, trigger to "Print" the real ticket
		this.printer.send();

		console.log("printTicketReceipt end");
	},
	printTuckshopReceipt: function( data ){
		console.log("printTuckshopReceipt start");

		this.printer.addPageBegin();

		// Set some common settings for the whole page
		// this.printer.addTextSmooth(true);
		this.printer.addTextLang("zh-hant");
		this.printer.addTextFont(this.printer.FONT_A);

		var line_height = 38;

		// Set the default line height in terms of "dot" --> 1 mm = 7.2 dots
		this.printer.addTextLineSpace( line_height ); // set the line height to 38 dots

		var number_of_items = 1;
		if( data.items.length > 0 ){
			number_of_items = data.items.length;
		}

		console.log( "number_of_items : " + number_of_items );

		var page_height = (17 + number_of_items) * line_height;

		console.log( "page_height : " + page_height );

		// Specify the size and position of the print area
		// for this setting, assuming there are 10 lines, each line height is 38 dots (around 5.3mm)
		this.printer.addPageArea(0, 86, 504, page_height);

		// row 1 + 2 
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(38);						// set the Y position of the print text, in term of dot
		this.printer.addTextDouble(false, true); 				// set the Double height of the print text, 1st parameter = double width, 2nd = double height
		// this.printer.addTextAlign(this.printer.ALIGN_CENTER);
		this.printer.addText("ACX Cinema");						// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);
		this.printer.addTextDouble(false, false); 				// reset the Double width / height of the print text

		// row 3
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(76);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_CENTER);
		this.printer.addText("Receipt");						// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 4
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(114);						// set the Y position of the print text, in term of dot
		this.printer.addText("No.");							// add the print text
		
		this.printer.addTextPosition(130); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(114);						// set the Y position of the print text, in term of dot
		this.printer.addText(": " + data.receipt_number);	// add the print text

		this.printer.addTextPosition(267); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(114);						// set the Y position of the print text, in term of dot
		this.printer.addText("Staff");							// add the print text
		
		this.printer.addTextPosition(375); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(114);						// set the Y position of the print text, in term of dot
		this.printer.addText(": " + data.staff);			// add the print text
		
		// row 5
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(152);						// set the Y position of the print text, in term of dot
		this.printer.addText("Cinema");							// add the print text
		
		this.printer.addTextPosition(130); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(152);						// set the Y position of the print text, in term of dot
		this.printer.addText(": " + data.cinema);			// add the print text

		this.printer.addTextPosition(267); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(152);						// set the Y position of the print text, in term of dot
		this.printer.addText("Time");							// add the print text
		
		this.printer.addTextPosition(375); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(152);						// set the Y position of the print text, in term of dot
		this.printer.addText(": " + data.purchase_time);	// add the print text

		// row 6
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(190);						// set the Y position of the print text, in term of dot
		this.printer.addText("Address");							// add the print text
		
		this.printer.addTextPosition(130); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(190);						// set the Y position of the print text, in term of dot
		this.printer.addText(": " + data.cinema_location);	// add the print text

		// row 7
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(228);						// set the Y position of the print text, in term of dot
		this.printer.addText("Tel");							// add the print text
		
		this.printer.addTextPosition(130); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(228);						// set the Y position of the print text, in term of dot
		this.printer.addText(": " + data.cinema_tel);		// add the print text

		// row 8
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(266);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_CENTER);
		this.printer.addText("-----------------------------------------------"); // add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 9
		// this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		// this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		// this.printer.addText("貨品");							// add the print text
		
		// this.printer.addTextPosition(252); 						// set the X position of the print text, in term of dot
		// this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		// this.printer.addText("數量");							// add the print text
		
		// this.printer.addTextPosition(360); 						// set the X position of the print text, in term of dot
		// this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		// this.printer.addText("單價");							// add the print text
		
		// this.printer.addTextPosition(432); 						// set the X position of the print text, in term of dot
		// this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		// this.printer.addText("金額");							// add the print text
		
		// row 10
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		this.printer.addText("Item");							// add the print text
		
		this.printer.addTextPosition(252); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		this.printer.addText("Qty");							// add the print text
		
		this.printer.addTextPosition(360); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		this.printer.addText("Price");							// add the print text
		
		this.printer.addTextPosition(432); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(304);						// set the Y position of the print text, in term of dot
		this.printer.addText("Amt");							// add the print text
		
		// row 11
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(342);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_CENTER);
		this.printer.addText("-----------------------------------------------"); // add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// items
		if( data.items.length > 0 ){
			for (var i = 0; i < data.items.length; i++) {
				var v_pos = 380 + (line_height * i);

				console.log( "v_pos : " + v_pos );

				this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
				this.printer.addTextVPosition(v_pos);						// set the Y position of the print text, in term of dot
				this.printer.addText(data.items[i].item);							// add the print text
				
				this.printer.addTextPosition(252); 						// set the X position of the print text, in term of dot
				this.printer.addTextVPosition(v_pos);						// set the Y position of the print text, in term of dot
				this.printer.addText(data.items[i].qty);							// add the print text
				
				this.printer.addTextPosition(360); 						// set the X position of the print text, in term of dot
				this.printer.addTextVPosition(v_pos);						// set the Y position of the print text, in term of dot
				this.printer.addText(data.items[i].unit_price);							// add the print text
				
				this.printer.addTextPosition(432); 						// set the X position of the print text, in term of dot
				this.printer.addTextVPosition(v_pos);						// set the Y position of the print text, in term of dot
				this.printer.addText(data.items[i].subtotal);							// add the print text
			}
		}

		// row 12
		this.printer.addTextPosition(360); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(608);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
		this.printer.addText("-------------"); // add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 13
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(646);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
		this.printer.addText("Total");							// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		this.printer.addTextPosition(360); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(646);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
		this.printer.addText(": $" + data.amount);			// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 14
		this.printer.addTextPosition(360); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(684);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
		this.printer.addText("============="); 					// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 15
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(722);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
		this.printer.addText(data.payment_method);			// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		this.printer.addTextPosition(360); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(722);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_RIGHT);
		this.printer.addText(": $" + data.amount);			// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 16
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(760);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_CENTER);
		this.printer.addText("-----------------------------------------------"); // add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// row 17
		this.printer.addTextPosition(0); 						// set the X position of the print text, in term of dot
		this.printer.addTextVPosition(798);						// set the Y position of the print text, in term of dot
		// this.printer.addTextAlign(this.printer.ALIGN_CENTER);
		this.printer.addText("Thank You!"); 						// add the print text
		// this.printer.addTextAlign(this.printer.ALIGN_LEFT);

		// End the "Page mode"
		this.printer.addPageEnd();	

		// Set the Feed Position to "Cutting edge" -- the Black marker at the back of ticket
		this.printer.addFeedPosition(this.printer.FEED_CUTTING);

		// Set to Cut the paper without feed 
		this.printer.addCut(this.printer.CUT_NO_FEED);

		// Send all set content in the buffer to the Print, trigger to "Print" the real ticket
		this.printer.send();

		console.log("printTuckshopReceipt end");
	},
	getStatusText: function(e, status) {

		console.log("getStatusText e :");
		console.log( e );

		console.log("getStatusText status :");
		console.log( status );

		var s = 'Status: \n';
		if (status & e.ASB_NO_RESPONSE) {
			s += ' No printer response\n';
		}
		if (status & e.ASB_PRINT_SUCCESS) {
			s += ' Print complete\n';
		}
		if (status & e.ASB_DRAWER_KICK) {
			s += ' Status of the drawer kick number 3 connector pin = "H"\n';
		}
		if (status & e.ASB_OFF_LINE) {
			s += ' Offline status\n';
		}
		if (status & e.ASB_COVER_OPEN) {
			s += ' Cover is open\n';
		}
		if (status & e.ASB_PAPER_FEED) {
			s += ' Paper feed switch is feeding paper\n';
		}
		if (status & e.ASB_WAIT_ON_LINE) {
			s += ' Waiting for online recovery\n';
		}
		if (status & e.ASB_PANEL_SWITCH) {
			s += ' Panel switch is ON\n';
		}
		if (status & e.ASB_MECHANICAL_ERR) {
			s += ' Mechanical error generated\n';
		}
		if (status & e.ASB_AUTOCUTTER_ERR) {
			s += ' Auto cutter error generated\n';
		}
		if (status & e.ASB_UNRECOVER_ERR) {
			s += ' Unrecoverable error generated\n';
		}
		if (status & e.ASB_AUTORECOVER_ERR) {
			s += ' Auto recovery error generated\n';
		}
		if (status & e.ASB_RECEIPT_NEAR_END) {
			s += ' No paper in the roll paper near end detector\n';
		}
		if (status & e.ASB_RECEIPT_END) {
			s += ' No paper in the roll paper end detector\n';
		}
		if (status & e.ASB_BUZZER) {
			s += ' Sounding the buzzer (certain model)\n';
		}
		if (status & e.ASB_SPOOLER_IS_STOPPED) {
			s += ' Stop the spooler\n';
		}
		if (status & e.ASB_BATTERY_OFFLINE) {
			s += ' Offline status due to the battery level\n';
		}
		return s;					
	}
};