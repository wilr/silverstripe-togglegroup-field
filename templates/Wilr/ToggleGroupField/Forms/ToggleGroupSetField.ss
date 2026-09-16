<ul $AttributesHTML>
	<% if $Options.Count %>
		<% loop $Options %>
			<li class="toggle-group__option">
				<input
					type="checkbox"
					id="$ID"
					class="toggle-group__input"
					name="$Name"
					value="$Value.ATT"
					<% if $isChecked %>checked<% end_if %>
					<% if $isDisabled %>disabled<% end_if %>
				/>
				<label for="$ID" class="toggle-group__label">$Title</label>
			</li>
		<% end_loop %>
	<% else %>
		<li><%t SilverStripe\\Forms\\CheckboxSetField_ss.NOOPTIONSAVAILABLE 'No options available' %></li>
	<% end_if %>
</ul>
