<div class="container-fluid">
    @if($client_data && in_array($client_data->user_code, ['RG00000038','RG00000060']))
        @include('pages.frontend.customer.client.olive-from')
    @else
        @include('pages.frontend.customer.client.customer-from')
    @endif    
</div>