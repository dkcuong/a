var TICKET_PRINTER = {
	printer: null,
	ePosDev: null,
	init_page: function() {
		console.log("[Ricky] Init Page Start");
		
		// declare the Printer obejct
		TICKET_PRINTER.ePosDev = new epson.ePOSDevice();

		console.log("[Ricky] Init Page > ePosDev");
		console.log( TICKET_PRINTER.ePosDev );

		// connect the Printer with specific IP address and port
		TICKET_PRINTER.ePosDev.connect('192.168.1.111', 8043, TICKET_PRINTER.cbConnect);
		TICKET_PRINTER.ePosDev.onreconnecting = TICKET_PRINTER.OnReconnecting;
		TICKET_PRINTER.ePosDev.onreconnect = TICKET_PRINTER.OnReconnect;
		TICKET_PRINTER.ePosDev.ondisconnect = TICKET_PRINTER.OnDisconnect;

		console.log("[Ricky] Init Page End");
	},
	cbConnect: function(data) {
		console.log("[Ricky] cbConnect Start");

		console.log("[Ricky] data :");
		console.log( data );

		if(data == 'OK' || data == 'SSL_CONNECT_OK') {

			console.log("[Ricky] cbConnect Start > SSL_CONNECT_OK :");
			console.log( TICKET_PRINTER.ePosDev );

			// create the Printer Device object
			TICKET_PRINTER.ePosDev.createDevice(
				'local_printer', 
				TICKET_PRINTER.ePosDev.DEVICE_TYPE_PRINTER,
				{'crypto':true, 'buffer':true}, 
				TICKET_PRINTER.cbCreateDevice_printer
			);
		} else {
			console.log("aaa :");
			console.log( data );
		}

		console.log("[Ricky] cbConnect End");
	},
	OnReconnecting: function() {
		console.log('onreconnecting');
	},
	OnReconnect: function() {
		console.log('onreconnect');
	},
	OnDisconnect: function() {
		console.log('ondisconnect');
	},
	cbCreateDevice_printer: function(devobj, retcode) {
		console.log("[Ricky] cbCreateDevice_printer Start");

		if( retcode == 'OK' ) {
			console.log( 'conCreateDevice OK');
			// config the Printer buffer
			TICKET_PRINTER.printer = devobj;
			TICKET_PRINTER.printer.timeout = 600000;
			/**
			 * Callback functions
			 */
			TICKET_PRINTER.printer.onreceive = function (res) {
				console.log( 'onreceive success : ' + res.success );
				console.log( 'onreceive code : ' + res.code );
				console.log( 'onreceive status : ' + res.status );
				console.log( 'onreceive battery : ' + res.battery );
				console.log( 'onreceive printjobid : ' + res.printjobid );
				console.log(TICKET_PRINTER.getStatusText(TICKET_PRINTER.printer, res));
			};
			TICKET_PRINTER.printer.onstatuschange = function (status) {
				console.log(TICKET_PRINTER.getStatusText(TICKET_PRINTER.printer, status));
			};
			TICKET_PRINTER.printer.ononline = function () {
				console.log('online');
			};
			TICKET_PRINTER.printer.onoffline = function () {
				console.log('offline');
			};
			TICKET_PRINTER.printer.onpoweroff = function () {
				console.log('poweroff');
			};
			TICKET_PRINTER.printer.oncoverok = function () {
				console.log('coverok');
			};
			TICKET_PRINTER.printer.oncoveropen = function () {
				console.log('coveropen');
			};
			TICKET_PRINTER.printer.onpaperok = function () {
				console.log('paperok');
			};
			TICKET_PRINTER.printer.onpapernearend = function () {
				console.log('papernearend');
			};
			TICKET_PRINTER.printer.onpaperend = function () {
				console.log('paperend');
			};
			TICKET_PRINTER.printer.ondrawerclosed = function () {
				console.log('drawerclosed');
			};
			TICKET_PRINTER.printer.ondraweropen = function () {
				console.log('draweropen');
			};
			TICKET_PRINTER.printer.onbatterystatuschange = function () {
				console.log('onbatterystatuschange');
			};
			TICKET_PRINTER.printer.onbatteryok = function () {
				console.log('onbatteryok');
			};
			TICKET_PRINTER.printer.onbatterylow = function () {
				console.log('onbatterylow');
			};

			// Actions to be done
			/**
			 * Print the Ticket
			 */
			// TICKET_PRINTER.printTicket();

			/**
			 * Print the Receipt
			 */
			TICKET_PRINTER.printReceipt();
			
			/**
			 * Kick the drawer out
			 */
			TICKET_PRINTER.kickDrawer();
		} else {
			console.log("bbb :");
			console.log( retcode );
		}

		console.log("[Ricky] cbCreateDevice_printer End");
	},
	getStatusText: function(e, status) {
	// get status text
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
		return s;
	},
	printPage: function(obj) {
		console.log("printTicket start");
		/**
		 * Grab the JSON object from Backend
		 * 
		 */
		var print_obj = {
			name : 'Tenet 天能',
			type : '2D',
			category : 'IIA',
			price : '45.0',
			schedule_date : 'Oct 12',
			schedule_year : '2020',
			schedule_time : '17:30',
			schedule_house : '4',
			schedule_seat : 'G10',
			payment_reference : '3742749823492398492',
			reprint : true
		};
		// <Set the "Page mode" starts>
		TICKET_PRINTER.printer.addPageBegin();
		// Set some common settings for the whole page
		TICKET_PRINTER.printer.addTextSmooth(true);
		TICKET_PRINTER.printer.addTextLang("zh-hant");
		TICKET_PRINTER.printer.addTextFont(TICKET_PRINTER.printer.FONT_A);
		/**
		 * Create 2 "Page area" for printing
		 */
		{
			// left page area
			// Set the default line height in terms of "dot" --> 1 mm = 7.2 dots
			TICKET_PRINTER.printer.addTextLineSpace(38); // set the line height to 38 dots
			// Specify the size and position of the print area
			// for this setting, assuming there are 10 lines, each line height is 38 dots (around 5.3mm)
			TICKET_PRINTER.printer.addPageArea(0, 86, 324, 380);
			// row 1
			TICKET_PRINTER.printer.addTextPosition(0);          // set the X position of the print text, in term of dot
			TICKET_PRINTER.printer.addTextVPosition(38);            // set the Y position of the print text, in term of dot
			TICKET_PRINTER.printer.addTextDouble(false, true);  // set the Double height of the print text, 1st parameter = double width, 2nd = double height
			TICKET_PRINTER.printer.addText(print_obj.name);     // add the print text
			TICKET_PRINTER.printer.addTextDouble(false, false);     // reset the Double width / height of the print text
			// row 2
			// row 3
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(76);
			TICKET_PRINTER.printer.addText("Price:");
			TICKET_PRINTER.printer.addTextPosition(180);
			TICKET_PRINTER.printer.addTextVPosition(76);
			TICKET_PRINTER.printer.addText("Category:");
			// row 4
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(114);
			TICKET_PRINTER.printer.addText("$" + print_obj.price);
			TICKET_PRINTER.printer.addTextPosition(180);
			TICKET_PRINTER.printer.addTextVPosition(114);
			TICKET_PRINTER.printer.addText(print_obj.category);
			// row 5
			TICKET_PRINTER.printer.addTextDouble(false, true);
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(152);
			TICKET_PRINTER.printer.addText(print_obj.schedule_date);
			TICKET_PRINTER.printer.addTextPosition(180);
			TICKET_PRINTER.printer.addTextVPosition(165);
			TICKET_PRINTER.printer.addTextSize(2, 2);
			TICKET_PRINTER.printer.addText(print_obj.schedule_time);
			TICKET_PRINTER.printer.addTextSize(1, 1);
			TICKET_PRINTER.printer.addTextDouble(false, false);
			// row 6
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(190);
			TICKET_PRINTER.printer.addText(print_obj.schedule_year);
			// row 7
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(228);
			TICKET_PRINTER.printer.addText("House:");
			TICKET_PRINTER.printer.addTextPosition(180);
			TICKET_PRINTER.printer.addTextVPosition(228);
			TICKET_PRINTER.printer.addText("Seat:");
			// row 8
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(266);
			TICKET_PRINTER.printer.addTextSize(2, 2);
			TICKET_PRINTER.printer.addText(print_obj.schedule_house);
			TICKET_PRINTER.printer.addTextSize(1, 1);
			TICKET_PRINTER.printer.addTextPosition(180);
			TICKET_PRINTER.printer.addTextVPosition(266);
			TICKET_PRINTER.printer.addTextSize(2, 2);
			TICKET_PRINTER.printer.addText(print_obj.schedule_seat);
			TICKET_PRINTER.printer.addTextSize(1, 1);
			// row 9
			// row 10
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(342);
			TICKET_PRINTER.printer.addText(print_obj.payment_reference);
			if( print_obj.reprint == true ){
				TICKET_PRINTER.printer.addTextPosition(240);
				TICKET_PRINTER.printer.addTextVPosition(342);
				TICKET_PRINTER.printer.addTextAlign(TICKET_PRINTER.printer.ALIGN_RIGHT);
				TICKET_PRINTER.printer.addText("REPRINT");
			}
		}
		{
			// right page area
			TICKET_PRINTER.printer.addTextLineSpace(38); // set the line height to 38 dots
			// Specify the size and position of the print area
			TICKET_PRINTER.printer.addPageArea(370, 90, 144, 365);
			// row 1
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(38);
			TICKET_PRINTER.printer.addTextDouble(false, true);
			TICKET_PRINTER.printer.addText(print_obj.name);
			TICKET_PRINTER.printer.addTextDouble(false, false);
			// row 2
			// row 3
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(90);
			TICKET_PRINTER.printer.addTextAlign(TICKET_PRINTER.printer.ALIGN_LEFT);
			TICKET_PRINTER.printer.addText("$" + print_obj.price);
			TICKET_PRINTER.printer.addTextPosition(72);
			TICKET_PRINTER.printer.addTextVPosition(90);
			TICKET_PRINTER.printer.addTextAlign(TICKET_PRINTER.printer.ALIGN_RIGHT);
			TICKET_PRINTER.printer.addText(print_obj.category);
			// row 4
			TICKET_PRINTER.printer.addTextDouble(false, true);
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(135);
			TICKET_PRINTER.printer.addText(print_obj.schedule_date + ", " +print_obj.schedule_year);
			TICKET_PRINTER.printer.addTextDouble(false, false);
			// row 5
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(180);
			TICKET_PRINTER.printer.addTextSize(2, 2);
			TICKET_PRINTER.printer.addText(print_obj.schedule_time);
			TICKET_PRINTER.printer.addTextSize(1, 1);
			TICKET_PRINTER.printer.addTextDouble(false, false);
			// row 6
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(220);
			TICKET_PRINTER.printer.addText("House: ");
				TICKET_PRINTER.printer.addTextSize(2, 2);
				TICKET_PRINTER.printer.addText(print_obj.schedule_house);
				TICKET_PRINTER.printer.addTextSize(1, 1);
			// row 7
			TICKET_PRINTER.printer.addTextPosition(0);
			TICKET_PRINTER.printer.addTextVPosition(260);
			TICKET_PRINTER.printer.addText("Seat : ");
				TICKET_PRINTER.printer.addTextSize(2, 2);
				TICKET_PRINTER.printer.addText(print_obj.schedule_seat);
				TICKET_PRINTER.printer.addTextSize(1, 1);
			// row 8
			// TICKET_PRINTER.printer.addTextPosition(0);
			// TICKET_PRINTER.printer.addTextVPosition(266);
			// TICKET_PRINTER.printer.addText(print_obj.payment_reference);
			// row 9
			// row 10
			if( print_obj.reprint == true ){
				TICKET_PRINTER.printer.addTextPosition(72);
				TICKET_PRINTER.printer.addTextVPosition(342);
				TICKET_PRINTER.printer.addTextAlign(TICKET_PRINTER.printer.ALIGN_RIGHT);
				TICKET_PRINTER.printer.addText("REPRINT");
			}
		}
		// End the "Page mode"
		TICKET_PRINTER.printer.addPageEnd();    
		console.log("printTicket end");

		// Set the Feed Position to "Cutting edge" -- the Black marker at the back of ticket
		TICKET_PRINTER.printer.addFeedPosition(TICKET_PRINTER.printer.FEED_CUTTING);
		// Set to Cut the paper without feed 
		TICKET_PRINTER.printer.addCut(TICKET_PRINTER.printer.CUT_NO_FEED);
		// Send all set content in the buffer to the Print, trigger to "Print" the real ticket
		TICKET_PRINTER.printer.send();
	},
	printReceipt: function(obj) {
		console.log("printReceipt start");

		// <Set the "Page mode" starts>
		TICKET_PRINTER.printer.addPageBegin();
		// Set some common settings for the whole page
		TICKET_PRINTER.printer.addTextSmooth(true);
		TICKET_PRINTER.printer.addTextLang("zh-hant");
		TICKET_PRINTER.printer.addTextFont(TICKET_PRINTER.printer.FONT_A);

		// Receipt Content

		// End the "Page mode"
		TICKET_PRINTER.printer.addPageEnd();    

		console.log("printReceipt end");

		// Set the Feed Position to "Cutting edge" -- the Black marker at the back of ticket
		TICKET_PRINTER.printer.addFeedPosition(TICKET_PRINTER.printer.FEED_CUTTING);
		// Set to Cut the paper without feed 
		TICKET_PRINTER.printer.addCut(TICKET_PRINTER.printer.CUT_NO_FEED);
		// Send all set content in the buffer to the Print, trigger to "Print" the real ticket
		TICKET_PRINTER.printer.send();
	},
	kickDrawer: function(obj) {
		console.log("kickDrawer start");

		// Receipt Content

		// TICKET_PRINTER.printer.drawerOpenLevel = TICKET_PRINTER.printer.DRAWER_OPEN_LEVEL_HIGH;
		{
			// open the drawer										
			TICKET_PRINTER.printer.addPulse(TICKET_PRINTER.printer.DRAWER_1, TICKET_PRINTER.printer.PULSE_500);
			// TICKET_PRINTER.printer.drawerOpenLevel = TICKET_PRINTER.printer.DRAWER_OPEN_LEVEL_LOW;
		}

		console.log("kickDrawer end");

		// Set the Feed Position to "Cutting edge" -- the Black marker at the back of ticket
		TICKET_PRINTER.printer.addFeedPosition(TICKET_PRINTER.printer.FEED_CUTTING);
		// Set to Cut the paper without feed 
		TICKET_PRINTER.printer.addCut(TICKET_PRINTER.printer.CUT_NO_FEED);
		// Send all set content in the buffer to the Print, trigger to "Print" the real ticket
		TICKET_PRINTER.printer.send();
	}
}