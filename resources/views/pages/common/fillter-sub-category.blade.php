@if($level == 1)
    <div class="form-group col-md-3 sub-cat-list">
        <select name="sub_category_name" class="form-control sub_category_name">
            <option value="">--- Select Sub Category ---</option>
            @forelse($cat_list as $cat)
                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
            @empty
            @endforelse
        </select>
    </div>
@elseif($level == 2)
    <div class="form-group col-md-3 sub-cat-list_1">
        <select name="sub_category_name_1" class="form-control sub_category_name_1">
            <option value="">--- Select Sub Category ---</option>
            @forelse($cat_list as $cat)
                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
            @empty
            @endforelse
        </select>
    </div>
@else
    <div class="form-group col-md-3 sub-cat-list_2">
        <select name="sub_category_name_2" class="form-control sub_category_name_2">
            <option value="">--- Select Sub Category ---</option>
            @forelse($cat_list as $cat)
                <option value="{{ $cat->code }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
            @empty
            @endforelse
        </select>
    </div>
@endif