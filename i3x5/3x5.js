/*
 * 3x5 javascript
 */

function showcard(divID) {
	var item = document.getElementById(divID);
	if (! item) { return; }
	if (item.className.indexOf('hidden') > -1) {
		item.className = item.className.replace(
			new RegExp('hidden'),'shown');
	} else if (item.className.indexOf('shown') > -1) {
		item.className = item.className.replace(
			new RegExp('shown'),'hidden');
	}
}
