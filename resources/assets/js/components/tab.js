const tab = (title, control, isActive = false) => (
  `<li role="presentation"${(isActive ? ' class="active"':'')}><a href="#${control}" aria-controls="${control}" role="tab" data-toggle="tab">${title.toLowerCase()}</a></li>`
);
export default tab;
