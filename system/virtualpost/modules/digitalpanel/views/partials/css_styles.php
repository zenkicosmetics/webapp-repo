<style>
    html,
    body {
        min-height: 100%;
        background: #366797;
    }

    body {
        padding: 20px 0;
    }

    #logo {
        position: fixed;
    }

    #addresses {
        border-bottom: 0;
    }

    #addresses_filter {
        display: none;
    }

    table.dataTable thead th, table.dataTable thead td {
        padding: 8px 10px;
    }

    #kbWrap {
        margin-top: 60px;
        text-align: center;
    }

    #keyboard {
        visibility: hidden;
    }

    .ui-keyboard.ui-widget-content.ui-widget {
        position: fixed !important;
    }

    .ui-keyboard {
        background: transparent;
        border: 0;
    }

    input.ui-keyboard-preview {
        padding: 5px;
        background: #fff;
        color: #000;
    }

    .ui-keyboard-button {
        min-height: 2em; width: 2em; min-width: 1em; margin: .1em; line-height: 2em;
    }

    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
        background: #fecc34;
        font-size: 2em;
        color: #000;
    }
</style>
