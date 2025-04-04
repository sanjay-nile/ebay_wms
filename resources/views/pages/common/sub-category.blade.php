@if($level == 1)
    <div class="form-group sub-cat-list">
        <label for="">Category Tier {{ $level }}</label>
        <select name="sub_category_name" class="form-control sub-cat">
            <option value="">--- Select Sub Category ---</option>
            @forelse($cat_list as $cat)
                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
            @empty
            @endforelse
        </select>
    </div>
@elseif($level == 2)
    <div class="form-group 2-tier-cat-list">
        <label for="">Category Tier {{ $level }}</label>
        <select name="sub_category_name_2" class="form-control 2-tier-cat">
            <option value="">--- Select Sub Category ---</option>
            @forelse($cat_list as $cat)
                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
            @empty
            @endforelse
        </select>
    </div>
@else
    <div class="form-group 3-tier-cat-list">
        <label for="">Category Tier {{ $level }}</label>
        <select name="sub_category_name_3" class="form-control 3-tier-cat">
            <option value="">--- Select Sub Category ---</option>
            @forelse($cat_list as $cat)
                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
            @empty
            @endforelse
        </select>
    </div>
@endif