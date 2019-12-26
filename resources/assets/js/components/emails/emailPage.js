import tab from '../tab'
import forwardPanel from './forwardPanel'
import fullPanel from './fullPanel'

const emailPage = (data) => {
  let first = true
  let html = `<form><ul class="nav nav-tabs" role="tablist">`
  for (let i in data) {
    if (data.hasOwnProperty(i) && i !== "testing")
      html = `${html}${tab(data[i]["email"], data[i]["email"].replace(/[@\-.]/g, ''), first)}`
    first = false
  }
  html = `${html}</ul>
  <div class="tab-content">
  `
  first = true
  for (let i in data) {
    if (data.hasOwnProperty(i) && i !== "testing") {
      if (data[i]["type"] === 'FULL') { html = `${html}${fullPanel(data[i], first)}` }
      if (data[i]["type"] === 'FORWARD' || data[i]["type"] === 'STATIC') { html = `${html}${forwardPanel(data[i], first)}` }
      first = false
    }
  }

  html = `${html}</div></form>`

  $('[data-toggle="tooltip"]').tooltip()

  return html
}

export default emailPage