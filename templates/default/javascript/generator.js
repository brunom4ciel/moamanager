



function populate(slct1, slct2) {
    var s1 = document.getElementById(slct1);
    var s2 = document.getElementById(slct2);
    s2.innerHTML = "";
    if (s1.value == "Cat1") {
        var optionArray = ["Subcat1", "Subcat1.1", "Subcat1.2"];
    } else if (s1.value == "Cat2") {
        var optionArray = ["Subcat2", "Subcat2.1", "Subcat2.2"];
    } else if (s1.value == "Cat3") {
        var optionArray = ["Subcat3", "Subcat3.1", "Subcat3.3"];
	}

	for (var option in optionArray) {
	    if (optionArray.hasOwnProperty(option)) {
	        var pair = optionArray[option];
	        var checkbox = document.createElement("input");
	        checkbox.type = "checkbox";
	        checkbox.name = pair;
	        checkbox.value = pair;
	        s2.appendChild(checkbox);
	
	        var label = document.createElement('label')
	        label.htmlFor = pair;
	        label.appendChild(document.createTextNode(pair));
	
	        s2.appendChild(label);
	        s2.appendChild(document.createElement("br"));    
	        }
	}
}

