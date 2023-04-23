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