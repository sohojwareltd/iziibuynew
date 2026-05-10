<div class="row">

    <div class="col-md-4">
        <x-form.input type="color" name="meta[shop_color]" label="{!! __('words.shop_top_color') !!}" :value="old('shop_color', $shop->shop_color ? $shop->shop_color : '#203864')" />
    </div>

    <div class="col-md-4">
        <x-form.input type="color" name="meta[shop_bottom_color]" label="{!! __('words.shop_bottom_color') !!}" :value="old('shop_bottom_color', $shop->shop_bottom_color ? $shop->shop_bottom_color : '#203864')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[header_color]" label="{!! __('words.shop_header_color') !!}" :value="old('header_color', $shop->header_color ?  $shop->header_color : '#96afe8')" />
    </div>

    <div class="col-md-6">
        <x-form.input type="color" name="meta[menu_color]" label="{!! __('words.menu_color') !!}" :value="old('menu_color', $shop->menu_color ? $shop->menu_color : '#203864')" />
    </div>
   
    <div class="col-md-6">
        <x-form.input type="color" name="meta[menu_hover_color]" label="{!! __('words.menu_hover_color') !!}" :value="old('menu_hover_color', $shop->menu_hover_color ? $shop->menu_hover_color : '#203864')" />

    </div>
  
    <div class="col-md-6">
        <x-form.input type="color" name="meta[top_header_language_text_color]" label="{!! __('words.top_header_language_text_color') !!}"
            :value="old('top_header_language_text_color', $shop->top_header_language_text_color ? $shop->top_header_language_text_color : '#203864')" />
    </div>
    <div class="col-md-6">
        <x-form.input type="color" name="meta[top_header_language_text_hover_color]" label="{!! __('words.top_header_language_text_hover_color') !!}"
            :value="old('top_header_language_text_hover_color', $shop->top_header_language_text_hover_color ? $shop->top_header_language_text_hover_color : '#96afe8')" />
    </div>

    <div class="col-md-6">
        <x-form.input type="color" name="meta[top_header_search_bar_text_color]" label="{!! __('words.top_header_search_bar_text_color') !!}"
            :value="old('top_header_search_bar_text_color', $shop->top_header_search_bar_text_color ? $shop->top_header_search_bar_text_color : '#203864')" />
    </div>
   
    <div class="col-md-6">
        <x-form.input type="color" name="meta[top_header_search_bar_bg_color]" label="{!! __('words.top_header_search_bar_bg_color') !!}"
            :value="old('top_header_search_bar_bg_color', $shop->top_header_search_bar_bg_color ? $shop->top_header_search_bar_bg_color : '#ffffff')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[top_header_search_btn_text_color]" label="{!! __('words.top_header_search_btn_text_color') !!}"
            :value="old('top_header_search_btn_text_color', $shop->top_header_search_btn_text_color ? $shop->top_header_search_btn_text_color : '#ffffff')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[top_header_search_btn_hover_color]" label="{!! __('words.top_header_search_btn_hover_color') !!}"
            :value="old('top_header_search_btn_hover_color', $shop->top_header_search_btn_hover_color ? $shop->top_header_search_btn_hover_color : '#000000')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[top_header_search_btn_bg_color]" label="{!! __('words.top_header_search_btn_bg_color') !!}"
            :value="old('top_header_search_btn_bg_color', $shop->top_header_search_btn_bg_color ? $shop->top_header_search_btn_bg_color : '#203864')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[footer_bg_color]" label="{!! __('words.footer_bg_color') !!}"
            :value="old('footer_bg_color', $shop->footer_bg_color ? $shop->footer_bg_color : '#96afe8')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[footer_text_color]" label="{!! __('words.footer_text_color') !!}"
            :value="old('footer_text_color', $shop->footer_text_color ? $shop->footer_text_color : '#ffffff')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[footer_text_hover_color]" label="{!! __('words.footer_text_hover_color') !!}"
            :value="old('footer_text_hover_color', $shop->footer_text_hover_color ? $shop->footer_text_hover_color : '#f1f1f1')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[buy_btn_bg_color]" label="{!! __('words.buy_btn_bg_color') !!}"
            :value="old('buy_btn_bg_color', $shop->buy_btn_bg_color ? $shop->buy_btn_bg_color : '#3fa24f')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[buy_btn_text_color]" label="{!! __('words.buy_btn_text_color') !!}"
            :value="old('buy_btn_text_color', $shop->buy_btn_text_color ? $shop->buy_btn_text_color : '#ffffff')" />
    </div>
    <div class="col-md-4">
        <x-form.input type="color" name="meta[buy_btn_hover_color]" label="{!! __('words.buy_btn_hover_color') !!}"
            :value="old('buy_btn_hover_color', $shop->buy_btn_hover_color ? $shop->buy_btn_hover_color : '#ffffff')" />
    </div>
</div>
