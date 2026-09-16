<ul $AttributesHTML>
	<% loop $Options %>
		<li class="toggle-group__option">
			<input
				type="radio"
				id="$ID"
				class="toggle-group__input"
				name="$Name"
				value="$Value.ATT"
				<% if $isChecked %>checked<% end_if %>
				<% if $isDisabled %>disabled<% end_if %>
				<% if $Up.Required %>required<% end_if %>
			/>
			<label for="$ID" class="toggle-group__label<% if $Up.IconsOnly %> toggle-group__label--icon-only<% end_if %>">
				<% if $HasIcon %>
					<% if $IconSvg %>
						<span class="toggle-group__icon" aria-hidden="true">$IconSvg</span>
					<% else_if $Icon %>
						<span class="toggle-group__icon font-icon-$Icon" aria-hidden="true"></span>
					<% end_if %>
				<% end_if %>
				<% if $Title %>
					<span class="toggle-group__title<% if $Up.IconsOnly %> toggle-group__title--sr-only<% end_if %>">$Title</span>
				<% end_if %>
			</label>
		</li>
	<% end_loop %>
</ul>
