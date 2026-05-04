{{-- Composant réutilisable : barre de recherche + tri + per_page --}}
{{-- Usage : <x-pagination-controls :action="route('xxx.index')" :params="$params" /> --}}

@props([
    'action',
    'params'      => [],
    'searchPlaceholder' => 'Rechercher...',
    'sortOptions' => [],   // [['value'=>'name','label'=>'Nom'], ...]
    'extraFilters'=> '',   // slot HTML supplémentaire
])

<form method="GET" action="{{ $action }}">
<div style="background:white;padding:1.25rem 1.5rem;border-radius:0.75rem;margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.1);">
    <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:flex-end;">

        {{-- Recherche --}}
        <div style="flex:1;min-width:95px;">
            <input type="text" name="search" class="form-input"
                   placeholder="{{ $searchPlaceholder }}"
                   value="{{ $params['search'] ?? '' }}"
                   style="margin-bottom:0;">
        </div>

        {{-- Tri --}}
        @if(count($sortOptions))
        <div style="display:flex;gap:0.5rem;">
            <select name="sort_by" class="form-select" style="margin-bottom:0;">
                @foreach($sortOptions as $opt)
                    <option value="{{ $opt['value'] }}" @selected(($params['sort_by'] ?? '') == $opt['value'])>
                        {{ $opt['label'] }}
                    </option>
                @endforeach
            </select>
            <select name="sort_dir" class="form-select" style="margin-bottom:0;width:auto;">
                <option value="desc" @selected(($params['sort_dir'] ?? 'desc') == 'desc')>↓ Desc</option>
                <option value="asc"  @selected(($params['sort_dir'] ?? 'desc') == 'asc')>↑ Asc</option>
            </select>
        </div>
        @endif

        {{-- Filtres supplémentaires passés en slot --}}
        {{ $slot }}

        {{-- Par page --}}
        <div>
            <select name="per_page" class="form-select" style="margin-bottom:0;width:auto;">
                @foreach([10,15,25,50,100] as $n)
                    <option value="{{ $n }}" @selected(($params['per_page'] ?? 15) == $n)>{{ $n }} / page</option>
                @endforeach
            </select>
        </div>

        {{-- Bouton --}}
        <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Filtrer</button>

        {{-- Reset --}}
        @if(array_filter(array_diff_key($params ?? [], ['page'=>1])))
            <a href="{{ $action }}" class="btn btn-secondary" style="white-space:nowrap;">Annuler</a>
        @endif

    </div>
</div>
</form>
