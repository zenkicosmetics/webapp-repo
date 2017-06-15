<style>
    /* keyboard - jQuery UI Widget */
    .ui-keyboard { padding: .3em; z-index: 16000; }
    .ui-keyboard-has-focus { z-index: 16001; }
    .ui-keyboard div { font-size: 1.1em; }
    .ui-keyboard-button { min-height: 2em; width: 2em; min-width: 1em; margin: .1em; cursor: pointer; overflow: hidden; line-height: 2em; -moz-user-focus: ignore; }
    .ui-keyboard-button span { padding: 0; margin: 0; white-space:nowrap; display: inline-block; }
    .ui-keyboard-button-endrow { clear: left; }
    .ui-keyboard-widekey { min-width: 4em; width: auto; }
    .ui-keyboard-space { width: 15em; }
    .ui-keyboard-space span, .ui-keyboard-empty span { font: 0/0 a; text-shadow: none; color: transparent; } /* see http://nicolasgallagher.com/another-css-image-replacement-technique/ */
    .ui-keyboard-preview-wrapper { text-align: center; }
    .ui-keyboard-preview { text-align: left; margin: 0 0 3px 0; display: inline; width: 99%;} /* width is calculated in IE, since 99% = 99% full browser width =( */
    .ui-keyboard-keyset { text-align: center; white-space: nowrap; }
    .ui-keyboard-input { text-align: left; }
    .ui-keyboard-input-current { -moz-box-shadow: 1px 1px 10px #5e9ed6; -webkit-box-shadow: 1px 1px 10px #5e9ed6; box-shadow: 1px 1px 10px #5e9ed6; }
    .ui-keyboard-placeholder { color: #888; }
    .ui-keyboard-nokeyboard { color: #888; border-color: #888; } /* disabled or readonly inputs, or use input[disabled='disabled'] { color: #f00; } */
    .ui-keyboard-button.disabled { opacity: 0.5; filter: alpha(opacity=50); } /* used by the acceptValid option to make the accept button appear faded */
    .ui-keyboard-spacer { display: inline-block; width: 1px; height: 0; }

    /* combo key styling - toggles diacritics on/off */
    .ui-keyboard-button.ui-keyboard-combo.ui-state-default { border-color: #ffaf0f; }

    /* (in)valid inputs */
    button.ui-keyboard-accept.ui-keyboard-valid-input { border-color: #0c0; background: #080; color: #fff; }
    button.ui-keyboard-accept.ui-keyboard-valid-input:hover { background: #0a0; }
    button.ui-keyboard-accept.ui-keyboard-invalid-input { border-color: #c00; background: #800; color: #fff; }
    button.ui-keyboard-accept.ui-keyboard-invalid-input:hover { background: #a00; }

    /*** jQuery Mobile definitions ***/
    /* jQuery Mobile styles - need wider buttons because of font size and text-overflow:ellipsis */
    .ui-body .ui-keyboard-button { width: 3em; display: inline-block; }
    .ui-body .ui-keyboard-widekey { width: 5.5em; }
    .ui-body .ui-keyboard-space { width: 15em; }
    .ui-body .ui-keyboard-space span { visibility: hidden; } /* hides the ellipsis */
    .ui-body .ui-keyboard-keyset { line-height: 0.5em; }
    .ui-body input.ui-input-text, .ui-body textarea.ui-input-text { width: 95%; }

    /* over-ride padding set by mobile ui theme - needed because the mobile script wraps button text with several more spans */
    .ui-body .ui-btn-inner { height: 2em; padding: 0.2em 0; margin: 0; }
    .ui-body .ui-btn { margin: 0; font-size: 13px; } /* mobile default size is 13px */

    /* Media Queries (optimized for jQuery UI themes; may be slightly off in jQuery Mobile themes) */
    /* 240 x 320 (small phone)  */
    @media all and (max-width: 319px) {
        .ui-keyboard div { font-size: 9px; }
        .ui-keyboard .ui-keyboard-input { font-size: 12px; }
        /* I don't own an iPhone so I have no idea how small this really is... is it even clickable with your finger? */
        .ui-body .ui-btn { margin: 0; font-size: 9px; }
        .ui-body .ui-keyboard-button { width: 1.8em; height: 2.5em; }
        .ui-body .ui-keyboard-widekey { width: 4em; }
        .ui-body .ui-keyboard-space { width: 8em; }
        .ui-body .ui-btn-inner { height: 2.5em; padding: 0.3em 0; }
    }

    /* 320 x 480 (iPhone)  */
    @media all and (min-width: 320px) and (max-width: 479px) {
        .ui-keyboard div { font-size: 9px; }
        .ui-keyboard .ui-keyboard-input { font-size: 14px; }
        /* I don't own an iPhone so I have no idea how small this really is... is it even clickable with your finger? */
        .ui-body .ui-btn { margin: 0; font-size: 11px; }
        .ui-body .ui-keyboard-button { width: 1.8em; height: 3em; }
        .ui-body .ui-keyboard-widekey { width: 4.5em; }
        .ui-body .ui-keyboard-space { width: 10em; }
        .ui-body .ui-btn-inner { height: 3em; padding: 0.7em 0; }
    }

    /* 480 x 640 (small tablet) */
    @media all and (min-width: 480px) and (max-width: 767px) {
        .ui-keyboard div { font-size: 13px; }
        .ui-keyboard .ui-keyboard-input { font-size: 14px; }
        .ui-body .ui-btn { margin: 0; font-size: 10px; }
        .ui-body .ui-keyboard-button { height: 2.5em; }
        .ui-body .ui-btn-inner { height: 2.5em; padding: 0.5em 0; }
    }
</style>
