<?php

use App\Support\PermissionGroups;
?>

@foreach ($sections as $sectionKey => $section)
    @php
        $sectionId = 'permSection_'.$sectionKey;
        $sectionCount = collect($section['groups'])->sum(fn ($items) => $items->count());
        $isOpen = $loop->first;
    @endphp

    <div class="card permission-accordion-card mb-3">
        <div class="card-header permission-accordion-header" data-bs-toggle="collapse" data-bs-target="#{{ $sectionId }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}" role="button">
            <div class="d-flex align-items-center gap-2">
                <span class="permission-section-icon">
                    <i class="{{ $section['icon'] }}"></i>
                </span>
                <div>
                    <h5 class="mb-0">{{ $section['label'] }}</h5>
                    <small class="text-muted">Sidebar section · {{ $sectionCount }} permission{{ $sectionCount === 1 ? '' : 's' }}</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if (! empty($selectable))
                    <label class="mb-0 small text-muted section-select-all" onclick="event.stopPropagation();">
                        <input type="checkbox" class="form-check-input sectionCheckbox me-1" data-section="{{ $sectionKey }}">
                        Select section
                    </label>
                @endif
                <i class="ri-arrow-down-s-line permission-accordion-chevron fs-4"></i>
            </div>
        </div>

        <div id="{{ $sectionId }}" class="collapse {{ $isOpen ? 'show' : '' }}" data-section="{{ $sectionKey }}">
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($section['groups'] as $groupKey => $items)
                        <div class="col-12 col-xl-6">
                            <div class="card permission-group-card h-100 mb-0" data-group="{{ $groupKey }}" data-section="{{ $sectionKey }}">
                                <div class="card-header d-flex align-items-center justify-content-between py-3">
                                    <div>
                                        <h6 class="mb-0">{{ PermissionGroups::groupLabel($groupKey) }}</h6>
                                        <small class="text-muted">{{ $items->count() }} permission{{ $items->count() === 1 ? '' : 's' }}</small>
                                    </div>
                                    @if (! empty($selectable))
                                        <label class="mb-0 form-check mb-0">
                                            <input
                                                id="{{ $groupKey }}_group"
                                                class="form-check-input grpCheckbox"
                                                type="checkbox"
                                                value="{{ $groupKey }}"
                                                data-group="{{ $groupKey }}"
                                                data-section="{{ $sectionKey }}"
                                            >
                                            <span class="ms-1 small">Select all</span>
                                        </label>
                                    @endif
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-nowrap align-middle mb-0 permission-table">
                                            <thead class="table-light">
                                                <tr>
                                                    @if (! empty($selectable))
                                                        <th style="width: 48px;" class="text-center">Use</th>
                                                    @else
                                                        <th style="width: 48px;" class="text-center">#</th>
                                                    @endif
                                                    <th style="width: 110px;">Action</th>
                                                    <th>Permission</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($items as $index => $item)
                                                    @php
                                                        $action = PermissionGroups::actionFromName($item->name);
                                                        $badgeClass = PermissionGroups::actionBadgeClass($action);
                                                        $checked = ! empty($role) && $role->hasPermissionTo($item->name);
                                                    @endphp
                                                    <tr>
                                                        @if (! empty($selectable))
                                                            <td class="text-center">
                                                                <label for="perm_{{ $item->id }}" class="mb-0 d-block">
                                                                    <input
                                                                        id="perm_{{ $item->id }}"
                                                                        type="checkbox"
                                                                        class="form-check-input itemCheckBox"
                                                                        name="permissions[]"
                                                                        value="{{ $item->id }}"
                                                                        data-grp="{{ $groupKey }}"
                                                                        data-section="{{ $sectionKey }}"
                                                                        {{ $checked ? 'checked' : '' }}
                                                                    >
                                                                </label>
                                                            </td>
                                                        @else
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                        @endif
                                                        <td>
                                                            <span class="badge {{ $badgeClass }}">{{ $action }}</span>
                                                        </td>
                                                        <td>
                                                            @if (! empty($selectable))
                                                                <label for="perm_{{ $item->id }}" class="mb-0 fw-medium">
                                                                    {{ $item->name }}
                                                                </label>
                                                            @else
                                                                <span class="fw-medium">{{ $item->name }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-muted">{{ $item->details ?: 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach
