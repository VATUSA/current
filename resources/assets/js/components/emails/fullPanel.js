const fullPanel = (data, isActive) => (
  `<div role="tabpanel" class="tab-pane${(isActive === true ? ' active' : '')}" id="${data['email'].replace(/[@\-.]/g, '')}">
    <div class="form-group">
      <label for="email_${data['email'].replace(/[@\.]/g, '')}">Address</label>
      <input type="email" readonly="true" class="form-control" id="email_${data['email'].replace(/[@\-.]/g, '')}" value="${data['email'].toLowerCase()}">
    </div>
    <div class="form-group">
      <label for="pass_${data['email'].replace(/[@\\.]/g, '')}">Password:</label>
      <p class="help-block">Must be at least 6 characters</p>
      <input type="password" class="form-control" id="password_${data['email'].replace(/[@\-.]/g, '')}">
    </div>
    <div class="form-group">
      <label for="pass2_${data['email'].replace(/[@\\\\.]/g, '')}">Confirm Password:</label>
      <input type="password" class="form-control" id="password2_${data['email'].replace(/[@\-.]/g, '')}">
    </div>
    <button type="button" class="btn btn-primary btnSave" data-email="${data['email']}" data-type="${data['type']}">Save</button>
  </div>`
)

export default fullPanel