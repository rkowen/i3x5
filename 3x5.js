/*
 * 3x5 javascript
 */
var downarrow = "<span style=\"font-size: larger\">&blacktriangledown;</span>";
var rightarrow ="<span style=\"font-size: larger\">&blacktriangleright;</span>";
var xshowmore = "<span style=\"font-size: larger\">&diams;</span>";
var xshowless = "<span style=\"font-size: larger\">&blacksquare;</span>";

function printpage() {
	window.print();
}

function hidecard(cid,tid) {
	var item = $("#"+cid);
	if (! item) { return; }
	var classlist = item.attr('class');
	if (! classlist) { return; }
	$(classlist.split(/\s+/)).each(function(index,xclass) {
		if (xclass === 'hidden') {
			item.removeClass('hidden');
			item.addClass('shown');
			$("#"+tid).html(downarrow);
			return;
		}
		if (xclass === 'shown') {
			item.removeClass('shown');
			item.addClass('hidden');
			$("#"+tid).html(rightarrow);
			return;
		}
	});
	return;
}
/* controller "button" has id=togid, the controlled elements have class=togid
 * global state is saved in a hidden span with id=togid + "_def"
 * the arrow span has class=togid + "_but"
 */
function hidecardall(togid) {
	var state = $("#"+togid+"_def").attr("class");
	var notstate = ((state=='hidden')?'shown':'hidden');
	var arrow = ((state == 'hidden') ? downarrow : rightarrow);

	var tlist = $("."+togid);
	$.each(tlist, function() {
		$(this).removeClass(state).addClass(notstate);
	});
	tlist = $("."+togid+"_but");
	$.each(tlist, function() {
		$(this).html(arrow);
	});

	$("#"+togid+"_def").removeClass(state).addClass(notstate);
	$("#"+togid).html(notstate=='hidden'?'Show':'Hide');
}

function showmore(cid) {
	var item = $("#cid"+cid);
	if (! item) { return; }
	var classlist = item.attr('class');
	if (! classlist) { return; }
	$(classlist.split(/\s+/)).each(function(index,xclass) {
		if (xclass === 'showmore') {
			item.removeClass('showmore');
			item.addClass('showless');
			$("#cid"+cid).html(xshowless);
			$("#span"+cid).dialog({
				autoOpen: false,
				resizable: false,
				height: "auto",
				width: "auto",
				dialogClass: "no-close",
				draggable: false,
				position: {
					my: "right top",
					at: "left top",
					of: "#cid"+cid
				},
				show: {
					effect: "blind",
					duration: 1000
				},
				hide: {
					effect: "explode",
					duration: 1000
				}
			});
			$.get({
				url:	"cardbatches.php?cid="+cid,
				success: function(result) {
					$("#span"+cid).html(result);
					$("#span"+cid).dialog('open');
				}});
			return;
		}
		if (xclass === 'showless') {
			item.removeClass('showless');
			item.addClass('showmore');
			$("#cid"+cid).html(xshowmore);
			$("#span"+cid).dialog('close');
			return;
		}
	});
	return;
}
