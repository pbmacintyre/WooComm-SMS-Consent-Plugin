/**
 * Copyright (C) 2021 - 2025 Paladin Business Solutions
 *
 */
window.onload = function() {
	embedd_options();
};

function reveal_it() {
	var y = document.getElementById("myGRCSite");    
    var z = document.getElementById("myGRCSecret");    

	if (y.type === "password") {
	  y.type = "text";
	}
	if (z.type === "password") {
	  z.type = "text";
	}
}

function hide_it() {
    var y = document.getElementById("myGRCSite");
    var z = document.getElementById("myGRCSecret");
	
	if (y.type === "text") {
	  y.type = "password";
	}
	if (z.type === "text") {
	  z.type = "password";
	}
}

function embedd_options() {
	var checkbox = document.getElementById("embedded");
	var tableRow = document.getElementById("embeddRow");

	if (checkbox.checked) {
		tableRow.style.display = "table-row"; // Show the row
	} else {
		tableRow.style.display = "none"; // Hide the row
	}
}

