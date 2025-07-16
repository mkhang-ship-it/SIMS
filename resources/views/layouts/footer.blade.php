@php
    use App\Models\Quote;
    $randomQuoteModel = Quote::where('status', 'active')->inRandomOrder()->first();
    $randomQuote = $randomQuoteModel ? '“' . $randomQuoteModel->quote . '”' : '';
@endphp
<footer class="footer mt-auto bg-white" role="contentinfo">
    <div class="container-fluid px-2">
        <div class="row align-items-center justify-content-center mx-0" style="min-height: 50px;">
            <div class="col-md-6 d-flex justify-content-md-start justify-content-center align-items-center pt-3 pt-md-0 pb-3 pb-md-0 px-3 px-md-0" style="height: 100%;">
                <div class="d-inline-flex align-items-center"
                    style="font-size: 14px; color: #fff; padding: .5rem .7rem; border-radius: 2rem; background-color: #78140f;">
                    <span class="inc-icon icon-20 me-2">
                        <img alt="Vietnam" src="{{ asset('assets/images/vn_flag.svg') }}"
                            style="width: 20px; height: 20px;">
                    </span>
                    <span class="text-center text-md-start w-100 d-block">{{ $randomQuote }}</span>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end justify-content-center align-items-center pb-3 pb-md-0 px-3 px-md-0" style="height: 100%;">
                <p class="mb-0 text-muted">Developed by <a href="javascript:void(0);"
                        class="text-dark fw-semibold">TechZone Club</a>.</p>
            </div>
        </div>
    </div>
</footer>
