setStyle();

function toggleStyle() {
  if (getCookie("style") == "dark") {
    setCookie("style", "light", 365);
  } else if (getCookie("style") == "light") {
    setCookie("style", "dark", 365);
  } else {
    setCookie("style", "light", 365);
  }
  setStyle();
}

function setStyle() {
  if (getCookie("style") == "light") {
    if (getCookie("acceptCookies") == "true") {
      setCookie("style", "light", 365);
    }
    document.querySelectorAll("link[href='/css/dark.css']")[0].disabled = true;
    document.querySelectorAll("link[href='/css/light.css']")[0].disabled = false;
  } else {
    if (getCookie("acceptCookies") == "true") {
      setCookie("style", "dark", 365);
    }
    document.querySelectorAll("link[href='/css/dark.css']")[0].disabled = false;
    document.querySelectorAll("link[href='/css/light.css']")[0].disabled = true;
  }
}

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}


function showPreview(event){
	var files = event.target.files;
	var preview = document.getElementById('preview');
	preview.innerHTML = '';
  var files1 = files
  var counter = 0;
  var f;
	for (i = 0, f; f = files[i]; i++) { 
    var reader = new FileReader();
    var hash;
    var f1 = files1;
    reader.onload = function(event) {
      var binary = event.target.result;
      hash = md5(binary).toString();
      var f = f1[counter];
      preview.innerHTML += ['<div class="col" id="', hash ,'"><div class="card prodcard cbg"><img src="', URL.createObjectURL(f), '" class="card-img-top img-fluid rounded" title="', escape(f.name), '" alt="', escape(f.name), '"><div class="card-body"><input type="number" value="1" name="room_image_id-', hash ,'" style="display: none;" required><div class="input-group pb-2"><span class="input-group-text" id="basic-addon1">Quelle</span><input type="text" class="form-control" placeholder="Quelle" value="" name="imgOwner-', hash ,'"></div><div class="input-group py-2"><span class="input-group-text" id="basic-addon1">Text</span><input type="text" class="form-control" placeholder="Text" value="" name="imgAlt-', hash ,'"></div></div></div></div>'].join('');
      counter++;
    };
    
    reader.readAsBinaryString(f); 
  }
}