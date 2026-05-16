@props(['variant' => 'header'])

@if($variant === 'footer')
    <svg width="44" height="44" viewBox="0 0 40 40" fill="none">
        <rect x="1" y="1" width="38" height="38" rx="12" fill="rgba(255,255,255,.12)"/>
        <circle cx="13" cy="11" r="4" fill="rgba(255,255,255,.7)"/>
        <path d="M10 15.5 Q10 20 10 23 L16 23 Q16 20 16 15.5 Q14.5 14.5 13 14.5 Q11.5 14.5 10 15.5Z" fill="rgba(255,255,255,.6)"/>
        <path d="M10 23 L9 30" stroke="rgba(255,255,255,.55)" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M15 23 L14.5 30" stroke="rgba(255,255,255,.55)" stroke-width="2.2" stroke-linecap="round"/>
        <circle cx="27" cy="11" r="4" fill="rgba(184,118,42,.85)"/>
        <path d="M24 15.5 Q24 20 24 23 L30 23 Q30 20 30 15.5 Q28.5 14.5 27 14.5 Q25.5 14.5 24 15.5Z" fill="rgba(184,118,42,.7)"/>
        <path d="M25.5 23 L25 30" stroke="rgba(184,118,42,.65)" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M29 23 L30 30" stroke="rgba(184,118,42,.65)" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M16 17 Q20 13 20 13" stroke="rgba(255,255,255,.5)" stroke-width="2" stroke-linecap="round" fill="none"/>
        <path d="M24 17 Q20 13 20 13" stroke="rgba(184,118,42,.65)" stroke-width="2" stroke-linecap="round" fill="none"/>
        <path d="M20 13 C20 13 18 10.5 18 9 C18 7.5 19 6.5 20 7.5 C21 6.5 22 7.5 22 9 C22 10.5 20 13 20 13Z" fill="rgba(184,118,42,.9)"/>
    </svg>
@else
    <svg class="logo-icon" viewBox="0 0 40 40" fill="none">
        <rect x="1" y="1" width="38" height="38" rx="12" fill="#0a6e7a" opacity=".08"/>
        <circle cx="13" cy="11" r="4" fill="#0a6e7a"/>
        <path d="M10 15.5 Q10 20 10 23 L16 23 Q16 20 16 15.5 Q14.5 14.5 13 14.5 Q11.5 14.5 10 15.5Z" fill="#0a6e7a" opacity=".9"/>
        <path d="M10 23 L9 30" stroke="#0a6e7a" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M15 23 L14.5 30" stroke="#0a6e7a" stroke-width="2.2" stroke-linecap="round"/>
        <circle cx="27" cy="11" r="4" fill="#b8762a"/>
        <path d="M24 15.5 Q24 20 24 23 L30 23 Q30 20 30 15.5 Q28.5 14.5 27 14.5 Q25.5 14.5 24 15.5Z" fill="#b8762a" opacity=".9"/>
        <path d="M25.5 23 L25 30" stroke="#b8762a" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M29 23 L30 30" stroke="#b8762a" stroke-width="2.2" stroke-linecap="round"/>
        <path d="M16 17 Q20 13 20 13" stroke="#0a6e7a" stroke-width="2" stroke-linecap="round" fill="none"/>
        <path d="M24 17 Q20 13 20 13" stroke="#b8762a" stroke-width="2" stroke-linecap="round" fill="none"/>
        <path d="M20 13 C20 13 18 10.5 18 9 C18 7.5 19 6.5 20 7.5 C21 6.5 22 7.5 22 9 C22 10.5 20 13 20 13Z" fill="#b8762a"/>
    </svg>
@endif
