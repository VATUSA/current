/* global $ */

import Basics from './components/privacy/basics';
import Info from './components/privacy/info';
import Use from './components/privacy/use';
import Shared from './components/privacy/shared';
import Cookie from './components/privacy/cookies';
import Optout from './components/privacy/optout';

const render = (x) => document.getElementById("privacybox").innerHTML = x;

$(document).ready(() => {
  $("#privacy").html(`
    <div class="container">
      <div class="col-md-2">
        <div class="list-group">
          <a href="#" class="list-group-item privacy-item" data-item="basics">Basics</a>
          <a href="#" class="list-group-item privacy-item" data-item="info">Information We Collect</a>
          <a href="#" class="list-group-item privacy-item" data-item="use">How We Use Information</a>
          <a href="#" class="list-group-item privacy-item" data-item="shared">Who We Share With</a>
          <a href="#" class="list-group-item privacy-item" data-item="cookie">How We Use Cookies</a>
          <a href="#" class="list-group-item privacy-item" data-item="optout">Opt Out</a>
        </div>
      </div>
      <div class="col-md-10" id="privacybox"></div>
  `);
  render(Basics);
}).on('click', '.privacy-item', (event) => {
  let item = $(event.currentTarget).data("item");
  switch(item) {
    case "basics":
      render(Basics);
      break;
    case "info":
      render(Info);
      break;
    case "use":
      render(Use);
      break;
    case "shared":
      render(Shared);
      break;
    case "cookie":
      render(Cookie);
      break;
    case "optout":
      render(Optout);
      break;
    default:
      render("Could not find that privacy information block");
      break;
  }
});
