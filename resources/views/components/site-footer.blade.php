@php($c=$community ?? [])
<svg class="icon-sprite" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">
    <symbol id="i-whatsapp" viewBox="0 0 24 24"><path d="M20 11.5a8 8 0 0 1-11.7 7.1L4 20l1.4-4.3A8 8 0 1 1 20 11.5Z"/><path d="M8.5 8.3c.3-.5.7-.5 1-.2l1 1.6c.2.3 0 .6-.4 1 .7 1.4 1.7 2.3 3.2 3 .3-.4.7-.6 1-.4l1.6 1c.4.3.3.7-.2 1-2 1.4-6.7-2.6-7.5-5.2-.2-.7-.1-1.3.3-1.8Z"/></symbol>
    <symbol id="i-location" viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 6 9 7 9-7"/></symbol>
    <symbol id="i-youtube" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3Z" fill="currentColor" stroke="none"/></symbol>
    <symbol id="i-instagram" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></symbol>
    <symbol id="i-tiktok" viewBox="0 0 24 24"><path d="M14 3v11a4.5 4.5 0 1 1-4.5-4.5M14 3c1 3 3 4.5 6 4.5"/></symbol>
    <symbol id="i-facebook" viewBox="0 0 24 24"><path d="M14 21v-8h3l.5-4H14V7c0-1 .5-1.5 1.5-1.5H18V2h-3c-3.5 0-5 2-5 5v2H7v4h3v8"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="7" r="3"/><path d="M2 20v-2a7 7 0 0 1 14 0v2M17 4a3 3 0 0 1 0 6m1 4c2.5.5 4 2 4 4v2"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 2v6m10-6v6M3 10h18m-9 4 2 2 4-4"/></symbol>
</svg>
<footer class="footer">
    <div class="container footer-grid">
        <div class="footer-intro">
            <a href="{{ route('home') }}" class="footer-logo"><img src="{{ asset('images/branding/logo.png') }}" alt="Logo Komunitas Literasi Remaja Tambun Selatan"></a>
            <p>Berdiri sejak 3 Oktober 2021<br>Tambun Selatan, Bekasi, Jawa Barat<br>Gerobak Angkasa: Perpustakaan Keliling</p>
            <div class="social-row">
                @foreach(['youtube'=>'YouTube','instagram'=>'Instagram','tiktok'=>'TikTok','facebook'=>'Facebook'] as $key=>$label)
                    @if($c['social_visibility'][$key] ?? true)
                        @if(!empty($c['social'][$key]))
                            <a href="{{ $c['social'][$key] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $label }}"><svg class="social-icon"><use href="#i-{{ $key }}"/></svg></a>
                        @else
                            <span class="social-placeholder" title="Tautan {{ $label }} belum diisi"><svg class="social-icon"><use href="#i-{{ $key }}"/></svg></span>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
        <div class="footer-navigation">
            <h3>Jelajahi</h3>
            <a href="{{ route('home') }}">Beranda Utama</a>
            <a href="{{ route('about') }}">Tentang Kami</a>
            <a href="{{ route('library.index') }}">Digital Library</a>
            <a href="{{ route('publication.index') }}">Publikasi &amp; Berita</a>
        </div>
        <div class="footer-contact">
            <h3>Kontak Kami</h3>
            <div class="footer-contact-line"><svg class="footer-icon"><use href="#i-whatsapp"/></svg>@if(!empty($c['contact']['whatsapp']))<a href="https://wa.me/{{ preg_replace('/\D+/','',$c['contact']['whatsapp']) }}">{{ $c['contact']['whatsapp'] }}</a>@else<span class="empty-contact">Nomor WhatsApp belum diisi</span>@endif</div>
            <div class="footer-contact-line"><svg class="footer-icon"><use href="#i-location"/></svg><span>{{ $c['contact']['location'] ?? 'Tambun Selatan, Kabupaten Bekasi, Jawa Barat' }}</span></div>
            <div class="footer-contact-line"><svg class="footer-icon"><use href="#i-mail"/></svg>@if(!empty($c['contact']['email']))<a href="mailto:{{ $c['contact']['email'] }}">{{ $c['contact']['email'] }}</a>@else<span class="empty-contact">Email belum diisi</span>@endif</div>
        </div>
    </div>
    <div class="footer-bottom">© {{ date('Y') }} Komunitas Literasi Remaja Tambun Selatan.</div>
</footer>