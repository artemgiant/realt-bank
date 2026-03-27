<tr>
    <td>{{ $permission->display_name ?? $permission->name }}</td>
    @foreach($roles as $role)
        <td>
            @if(isset($scopedPermissions[$permission->name]))
                @php
                    $scope = $scopedPermissions[$permission->name];
                    $hasCompany = $permissionsMatrix[$scope['company']][$role->id] ?? false;
                    $hasOffice = $permissionsMatrix[$scope['office']][$role->id] ?? false;
                    $hasBase = $permissionsMatrix[$permission->name][$role->id] ?? false;

                    if ($hasCompany) $currentScope = 'company';
                    elseif ($hasOffice) $currentScope = 'office';
                    elseif ($hasBase) $currentScope = 'own';
                    else $currentScope = 'none';
                @endphp
                <select class="perm-scope-select"
                        data-permission="{{ $permission->name }}"
                        data-perm-office="{{ $scope['office'] }}"
                        data-perm-company="{{ $scope['company'] }}"
                        data-role="{{ $role->id }}">
                    <option value="none" {{ $currentScope === 'none' ? 'selected' : '' }}>—</option>
                    <option value="own" {{ $currentScope === 'own' ? 'selected' : '' }}>Свои</option>
                    <option value="office" {{ $currentScope === 'office' ? 'selected' : '' }}>Офис</option>
                    <option value="company" {{ $currentScope === 'company' ? 'selected' : '' }}>Компания</option>
                </select>
            @else
                <input type="checkbox"
                       class="perm-check"
                       data-permission="{{ $permission->name }}"
                       data-role="{{ $role->id }}"
                       {{ ($permissionsMatrix[$permission->name][$role->id] ?? false) ? 'checked' : '' }}>
            @endif
        </td>
    @endforeach
</tr>
