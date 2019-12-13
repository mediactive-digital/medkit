
<!-- RolePermissions Field -->
<div class="form-group">
	<?php
	// $role->permissions()
	$indiceName	 = "";
	$first		 = true;
	foreach ($permissions AS $permission) {
		$aName = explode('_', $permission->name);

		if ($indiceName != $aName[0]) {
			if (!$first) {
				// fermeture des balise ul
				echo "
                    </ul>
              </div>
            </div>
          </div>";
			}

			// Ouverture balise ul
			echo '<div class="container">
            <h3 id="h3-listgroups" class="h3-marks">' . $aName[0] . '</h3>

            <div class="row one-ui-kit-example-container">
              <div class="col-12">
                  <ul class="list-group">
					  ';
		}
		$first		 = false;
		$indiceName	 = $aName[0];
		?>
		<li class="list-group-item d-flex justify-content-between align-items-center">
			<?php
			echo $permission->name;
			$match		 = $rolePermissions->firstWhere('name', $permission->name);

			if ($match) {
				?>
				<span class="badge badge-success badge-pill"><i class="material-icons">check</i></span>
				<?php
			} else {
				?>
				<span class="badge badge-danger badge-pill"><i class="material-icons">close</i></span>
				<?php
			}
			?>
		</li>
		<?php
	}
	?> </ul>
</div>
</div>
</div>
</div>
