{{-- Updated: Footer now stretches full width on desktop, removing side gaps. Used container-fluid and px-0 for edge-to-edge layout. On mobile, all content is centered.
     Now displays a random patriotic quote from the quotes table (status = active) on each page load. --}}
@php
    use App\Models\Quote;
    $randomQuoteModel = Quote::where('status', 'active')->inRandomOrder()->first();
    $randomQuote = $randomQuoteModel ? '“' . $randomQuoteModel->quote . '”' : '';
@endphp
<footer class="footer mt-auto pt-3 bg-white" role="contentinfo">
    <div class="container-fluid px-0">
        <div class="row align-items-center mx-0">
            <div class="col-md-6 d-flex justify-content-md-start justify-content-center pb-3">
                <div class="d-inline-flex align-items-center"
                    style="font-size: 14px; color: #fff; padding: .5rem .7rem; border-radius: 2rem; background-color: #78140f;">
                    <span class="inc-icon icon-20 me-2">
                        <img alt="Vietnam" src="{{ asset('assets/images/vn_flag.svg') }}"
                            style="width: 20px; height: 20px;">
                    </span>
                    <span class="text-center text-md-start w-100 d-block">{{ $randomQuote }}</span>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end justify-content-center pb-3">
                <p class="mb-0 text-muted">Developed by <a href="javascript:void(0);"
                        class="text-dark fw-semibold">TechZone Club</a>.</p>
            </div>
        </div>
    </div>
</footer>
