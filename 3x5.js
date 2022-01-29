/*
 * 3x5 javascript
 */
var downarrow  = "<span style=\"font-size: larger\">&blacktriangledown;</span>";
var rightarrow ="<span style=\"font-size: larger\">&blacktriangleright;</span>";
var xshowmore	= "<span style=\"font-size: larger\">&diams;</span>";
var xshowless	= "<span style=\"font-size: larger\">&blacksquare;</span>";
var xopeneye	= "<span style=\"font-size: larger\">&gtdot;</span>";
var xcloseeye	= "<span style=\"font-size: larger\">&NotSucceeds;</span>";

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

function viewpass() {
	var plist = $("input.password");
	var peye = $("#passeye");
	var state = peye.data('state');
	if (state === 'open') {
		$.each(plist, function(){
			$(this).attr('type','text');
		});
		peye.html(xcloseeye)
		peye.data("state","close")
	} else if (state === 'close') {
		$.each(plist, function(){
			$(this).attr('type','password');
		});
		peye.html(xopeneye)
		peye.data("state","open")
	}
}

function viewmenu() {
	var peye = $("#menueye");
	var state = peye.attr('data-state');
	if (state === 'open') {
		peye.html(xcloseeye)
		peye.attr('data-state',"close")
		$("#bodyleft").removeClass("hidden");
		FrameSize();
		$(window).resize(function() { FrameSize(); });
	} else if (state === 'close') {
		peye.html(xopeneye)
		peye.attr('data-state',"open")
		$("#bodyleft").addClass("hidden");
		FrameSize($("#body"),10);
		$(window).resize(function() { FrameSize($("#body"),10); });
	}
}

function openhelp() {
	// on load of indexH.php expand the menu
	var peye = $("#menueye",parent.document);
	var state = peye.attr('data-state');
	if (state === 'open') {
		peye.html(xcloseeye)
		peye.attr('data-state',"close")
		$("#bodyleft",parent.document).removeClass("hidden");
		FrameSize(parent.document);
		$(window).resize(function() { FrameSize(parent.document); });
	}
}

function XXX () {
//	var wid = window.innerWidth;
//	var hit = window.innerHeight;
	var top = window.top;
	var wid = top.innerWidth;
	var hit = top.innerHeight;
	//alert("width = "+wid+" height = "+hit);
	console.log("window width = "+wid+" height = "+hit
	+" length = "+top.length
	+" name = "+top.name);
}

function FrameSize(ctext, w = 250) {
	var top = window.top;
	var wid = top.innerWidth - 32;
	var hit = top.innerHeight;
	var context = (ctext !== 'undefined' ? ctext : $("#body"));
	var page = $("#page",context);
	var bodyleft = $("#bodyleft",context);
	var bodyright = $("#bodyright",context);
	var main = $("#main",context);
	var menu = $("#menu",context);
	var helptext = $("#helptext",context);
/* set some iframe widths (else default 150x300 */
	page.height(hit);
	if (bodyleft.css("display") == "none") {
		// probably in print mode
		bodyleft.height(hit).width(0);
	} else {
		bodyleft.height(hit).width(Math.min(w,wid*.25));
	}
	menu.height(hit*.6).width(bodyleft.width());
	bodyright.height(hit).width(wid -4 - bodyleft.width());
	main.width(bodyright.width()).height(hit);
	helptext.width(bodyleft.width()).height(hit*.4);
}

