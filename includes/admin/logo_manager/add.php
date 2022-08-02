<div class="wrapper">
  <div class="container mt-3">

    <h3>Add Logo</h3>

    <div class="col">
      <div class="card p-0 mx-auto">
        <div class="card-header card-default-color">
            <h5>Logo Details</h5>
        </div>
        <div class="card-body">

            <div class="mb-4">
                <img src="<?php echo HYBRID_DIR_URL.'includes/admin/assets/images/placeholder.png' ?>" class="img-thumbnail logo-thumbnail" width="200" alt="">
                <button class="btn btn-primary upload-image">Add logo</button>
            </div>

            <div class="mb-4">
              <label for="entry_type" class="form-label">Entry Type:</label>
              <br/>
              <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="entry_type" id="entry_type_team" value="1">
                    <label class="form-check-label" for="entry_type_team">Team/Club</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="entry_type" id="entry_type_event" value="2">
                    <label class="form-check-label" for="entry_type_event">Event</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="entry_type" id="entry_type_organization" value="3">
                    <label class="form-check-label" for="entry_type_organization">Organization</label>
                </div>
            </div>

            <div class="mb-4">
              <label for="team_club_name" class="form-label">Team/Club Name:</label>
              <input type="text" class="form-control" name="team_club_name" id="team_club_name">
            </div>

            <div class="mb-4">
              <label for="type_of_sport" class="form-label">Type of Sport:</label>
              <select class="form-select form-select-lg" name="type_of_sport">
                <option selected>Please select</option>
                <option value="1">Football (Soccer)</option>
                <option value="2">Rugby Leage</option>
                <option value="3">Rugby Union</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="entry_type" class="form-label">Level:</label>
              <br/>
              <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="logo_manager_level" id="level_international" value="1">
                    <label class="form-check-label" for="level_international">International</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="logo_manager_level" id="level_national" value="2">
                    <label class="form-check-label" for="level_national">National</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="logo_manager_level" id="level_club" value="3">
                    <label class="form-check-label" for="level_club">Club</label>
                </div>
            </div>

          <button type="submit" class="btn btn-success btn-submit-logo">Submit</button>
        </div>
      </div>
    </div>
  </div>
</div>
