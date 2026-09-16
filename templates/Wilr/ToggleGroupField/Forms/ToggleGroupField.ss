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
			<label for="$ID" class="toggle-group__label">$Title</label>
		</li>
	<% end_loop %>
</ul>
