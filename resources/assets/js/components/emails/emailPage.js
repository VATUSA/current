import tab from '../tab';
import forwardPanel from './forwardPanel';
import fullPanel from './fullPanel';

const emailPage = (data) => {
  let first = true;
  let html = `<form><ul class="nav nav-tabs" role="tablist">`;
  data.map((i, v) => {
    html = `${html}${tab(i.email,i.email.replace(/[@\-.]/g, ''),first)}`;
    first = false;
  });
  html = `${html}</ul>
  <div class="tab-content">
  `;
  first = true
  data.map((i, v) => {
    i.isActive = first;
    if (i.type === "FULL") { html = `${html}${fullPanel(i)}`; }
    if (i.type === "FORWARD" || i.type === "STATIC") { html = `${html}${forwardPanel(i)}`; }
    first = false;
  });
  html = `${html}</div></form>`;

  $('[data-toggle="tooltip"]').tooltip();

  return html;
};

export default emailPage;