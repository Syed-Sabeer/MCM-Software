<table class="document-header">
    <tr>
        <td class="brand-column">
            @if($logoSource)
                <img src="{{ $logoSource }}" alt="{{ $companyName }}" class="logo">
            @else
                <div class="company-name">{{ $companyName }}</div>
            @endif

            @if(! empty($companyLines))
                <div class="company-details">
                    @foreach($companyLines as $line)<div>{{ $line }}</div>@endforeach
                </div>
            @endif
        </td>
        <td class="document-column">
            <div class="document-title">{{ $documentTitle }}</div>
            <div class="document-number">{{ $documentNumber }}</div>

            @if(! empty($documentMeta))
                <table class="meta-table">
                    @foreach($documentMeta as $meta)
                        <tr><td class="meta-label">{{ $meta['label'] }}</td><td class="meta-value">{{ $meta['value'] }}</td></tr>
                    @endforeach
                </table>
            @endif
        </td>
    </tr>
</table>
<div class="accent-rule"></div>
