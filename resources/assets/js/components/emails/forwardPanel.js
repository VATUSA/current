const forwardPanel = (data, isActive) => (
  `<div role="tabpanel" class="tab-pane${(isActive === true ? ' active':'')}" id="${data["email"].replace(/[@\-.]/g,'')}">
          <div class="form-group">
            <label for="email_${data["email"].replace(/[@\.]/g,'')}">Address</label>
            <input type="email" readonly="true" class="form-control" id="email_${data["email"].replace(/[@\-.]/g, '')}" value="${data["email"].toLowerCase()}">
          </div>
          <div class="form-group">
            <label for="dest_${data["email"].replace(/[@\-.]/g, '')}">Forward To: (separate multiple emails with a ,)</label>
            <input type="text" class="form-control" id="destination_${data["email"].replace(/[@\-.]/g, '')}" value="${data["destination"]}">
          </div>
          <div class="form-group">
            <label for="static_${data["email"].replace(/[@\-.]/g,'')}">Static <i class="fa fa-question-circle" data-toggle="tooltip" title="Static forwards are forwards that do not change during staff changes and can be used for facility-specific addresses, mailing lists, etc"></i></label>
            <select class="form-control staticselect" data-id="${data["email"].replace(/[@\-.]/g, '')}" id="static_${data["email"].replace(/[@\-.]/g, '')}">
              <option${(data.type === "STATIC") ? ' selected="true"' : ''}>Yes</option>
              <option${(data.type !== "STATIC") ? ' selected="true"' : ''}>No</option>
            </select>
          </div>
          <button type="button" class="btn btn-primary btnSave" data-email="${data["email"]}" data-type="${data["type"]}">Save</button>
     </div>`
);

export default forwardPanel;