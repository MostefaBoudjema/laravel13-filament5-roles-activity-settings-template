@php
    $modules       = $getModules();
    $actions       = $getActions();
    $selectedPerms = $getSelectedPermissions();
    $statePath     = $getStatePath();
    $isDisabled    = $isDisabled();

    $actionLabels = [
        'view_any' => __('View Any'),
        'view'     => __('View'),
        'create'   => __('Create'),
        'update'   => __('Update'),
        'delete'   => __('Delete'),
    ];

    $allPermNames = collect($modules)->flatMap(fn($a) => array_values($a))->values()->toArray();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <style>
        /* ── Permission Matrix ─────────────────────────────────────── */
        .pm-wrap { overflow-x: auto; border-radius: .75rem; border: 1px solid rgba(0,0,0,.08); box-shadow: 0 2px 12px rgba(0,0,0,.05); background: #fff; }
        .dark .pm-wrap { border: 1px solid rgba(255,255,255,.08); box-shadow: 0 2px 12px rgba(0,0,0,.25); background: transparent; }
        .pm-table { width: 100%; border-collapse: collapse; font-size: .825rem; }

        /* header */
        .pm-thead tr { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); }
        .pm-th { padding: .65rem .75rem; color: #fff; font-weight: 600; font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; text-align: center; white-space: nowrap; }
        .pm-th-module { text-align: left; min-width: 160px; }
        .pm-th-action { min-width: 88px; }

        /* rows */
        .pm-row:nth-child(odd)  { background: rgba(0,0,0,.02); }
        .pm-row:nth-child(even) { background: rgba(0,0,0,.04); }
        .pm-row:hover { background: rgba(99,102,241,.08) !important; }

        .dark .pm-row:nth-child(odd)  { background: rgba(255,255,255,.03); }
        .dark .pm-row:nth-child(even) { background: rgba(255,255,255,.065); }
        .dark .pm-row:hover { background: rgba(99,102,241,.15) !important; }

        .pm-td { padding: .55rem .75rem; border-bottom: 1px solid rgba(0,0,0,.06); text-align: center; vertical-align: middle; }
        .dark .pm-td { border-bottom: 1px solid rgba(255,255,255,.06); }
        .pm-td-module { text-align: left; }

        /* module label */
        .pm-module-name { display: flex; align-items: center; gap: .5rem; }
        .pm-module-name span { color: #334155; font-weight: 500; text-transform: capitalize; }
        .dark .pm-module-name span { color: #e2e8f0; }

        /* ── Checkbox styling ───────────────────────────── */
        .pm-cb { appearance: none; -webkit-appearance: none; width: 18px; height: 18px; border-radius: 4px; border: 2px solid #6366f1; background: #fff; cursor: pointer; position: relative; transition: background .15s, border-color .15s, transform .15s; flex-shrink: 0; display: block; }
        .dark .pm-cb { background: transparent; }
        .pm-cb:checked { background: #6366f1; border-color: #6366f1; }
        .pm-cb:checked::after { content: ''; display: block; position: absolute; left: 4px; top: 1px; width: 6px; height: 10px; border: 2px solid #fff; border-top: none; border-left: none; transform: rotate(45deg); }
        .pm-cb:hover:not(:disabled) { border-color: #818cf8; transform: scale(1.1); }
        .pm-cb:checked:hover:not(:disabled) { background: #4f46e5; border-color: #4f46e5; }
        .pm-cb:disabled { opacity: .45; cursor: not-allowed; }
        .pm-cb:focus-visible { outline: 2px solid #818cf8; outline-offset: 2px; }

        /* row / column toggles (smaller) */
        .pm-cb-sm { width: 15px; height: 15px; border-radius: 3px; }
        .pm-cb-sm:checked::after { left: 3px; top: 0px; width: 5px; height: 9px; }

        /* header toggle (on dark bg) */
        .pm-cb-hd { border-color: rgba(255,255,255,.6); background: transparent; }
        .pm-cb-hd:checked { background: #fff; border-color: #fff; }
        .pm-cb-hd:checked::after { border-color: #6366f1; }
        .pm-cb-hd:hover:not(:disabled) { border-color: #fff; }

        /* "N/A" placeholder */
        .pm-na { display: inline-block; width: 18px; height: 18px; border-radius: 4px; background: rgba(0,0,0,.07); opacity: .4; }
        .dark .pm-na { background: rgba(255,255,255,.07); }

        /* center checkboxes */
        .pm-center { display: flex; justify-content: center; align-items: center; }
    </style>

    <div
        x-data="{
            sel: @js($selectedPerms),

            sync() {
                $wire.set(@js($statePath), this.sel);
            },

            has(p) { return this.sel.includes(p); },

            toggle(p) {
                const i = this.sel.indexOf(p);
                i === -1 ? this.sel.push(p) : this.sel.splice(i, 1);
                this.sync();
            },

            rowAll(perms) { return perms.length > 0 && perms.every(p => this.sel.includes(p)); },

            toggleRow(perms) {
                const all = this.rowAll(perms);
                perms.forEach(p => {
                    const i = this.sel.indexOf(p);
                    if (all  && i !== -1) this.sel.splice(i, 1);
                    if (!all && i === -1) this.sel.push(p);
                });
                this.sync();
            },

            colAll(perms) { return perms.length > 0 && perms.every(p => this.sel.includes(p)); },

            toggleCol(perms) {
                const all = this.colAll(perms);
                perms.forEach(p => {
                    const i = this.sel.indexOf(p);
                    if (all  && i !== -1) this.sel.splice(i, 1);
                    if (!all && i === -1) this.sel.push(p);
                });
                this.sync();
            },

            globalAll() { return @js($allPermNames).every(p => this.sel.includes(p)); },

            toggleAll() {
                const all = this.globalAll();
                @js($allPermNames).forEach(p => {
                    const i = this.sel.indexOf(p);
                    if (all  && i !== -1) this.sel.splice(i, 1);
                    if (!all && i === -1) this.sel.push(p);
                });
                this.sync();
            },
        }"
    >
        <div class="pm-wrap">
            <table class="pm-table">

                {{-- ══════════ HEADER ══════════ --}}
                <thead class="pm-thead">
                    <tr>
                        <th class="pm-th pm-th-module">{{ __('Module') }}</th>

                        @foreach ($actions as $action)
                            @php
                                $colPerms = collect($modules)
                                    ->map(fn($a) => $a[$action] ?? null)
                                    ->filter()->values()->toArray();
                            @endphp
                            <th class="pm-th pm-th-action">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                                    <span>{{ $actionLabels[$action] ?? $action }}</span>
                                    <div class="pm-center">
                                        <input
                                            type="checkbox"
                                            class="pm-cb pm-cb-sm pm-cb-hd"
                                            :checked="colAll(@js($colPerms))"
                                            @change="toggleCol(@js($colPerms))"
                                            title="{{ __('Toggle column') }}"
                                            {{ $isDisabled ? 'disabled' : '' }}
                                        />
                                    </div>
                                </div>
                            </th>
                        @endforeach

                        {{-- Global "All" column --}}
                        <th class="pm-th pm-th-action">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                                <span>{{ __('All') }}</span>
                                <div class="pm-center">
                                    <input
                                        type="checkbox"
                                        class="pm-cb pm-cb-sm pm-cb-hd"
                                        :checked="globalAll()"
                                        @change="toggleAll()"
                                        title="{{ __('Toggle all') }}"
                                        {{ $isDisabled ? 'disabled' : '' }}
                                    />
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>

                {{-- ══════════ ROWS ══════════ --}}
                <tbody>
                    @foreach ($modules as $module => $moduleActions)
                        @php $rowPerms = array_values($moduleActions); @endphp
                        <tr class="pm-row">

                            {{-- Module name + row toggle --}}
                            <td class="pm-td pm-td-module">
                                <div class="pm-module-name">
                                    <input
                                        type="checkbox"
                                        class="pm-cb pm-cb-sm"
                                        :checked="rowAll(@js($rowPerms))"
                                        @change="toggleRow(@js($rowPerms))"
                                        title="{{ __('Toggle row') }}"
                                        {{ $isDisabled ? 'disabled' : '' }}
                                    />
                                    <span>{{ __(ucwords(str_replace('_', ' ', $module))) }}</span>
                                </div>
                            </td> 

                            {{-- Action checkboxes --}}
                            @foreach ($actions as $action)
                                @php $perm = $moduleActions[$action] ?? null; @endphp
                                <td class="pm-td">
                                    @if ($perm)
                                        <div class="pm-center">
                                            <input
                                                type="checkbox"
                                                class="pm-cb"
                                                :checked="has(@js($perm))"
                                                @change="toggle(@js($perm))"
                                                title="{{ $perm }}"
                                                {{ $isDisabled ? 'disabled' : '' }}
                                            />
                                        </div>
                                    @else
                                        <div class="pm-center"><span class="pm-na"></span></div>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Row "All" --}}
                            <td class="pm-td">
                                <div class="pm-center">
                                    <input
                                        type="checkbox"
                                        class="pm-cb"
                                        :checked="rowAll(@js($rowPerms))"
                                        @change="toggleRow(@js($rowPerms))"
                                        title="{{ __('Toggle row') }}"
                                        {{ $isDisabled ? 'disabled' : '' }}
                                    />
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</x-dynamic-component>
