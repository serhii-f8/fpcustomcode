{*FPCustomCode v1.0.0*}
{*https://github.com/flexpik/ps-custom-code*}
{*Copyright (c) 2022 FlexPik.com*}
{*Released under the MIT license*}
{*Date: 2022-11-06*}


{if ($custom_code.css || $custom_code.global_css)}
  <style>{$custom_code.global_css nofilter} {$custom_code.css nofilter}</style>
{/if}

{if $custom_code.js}
  <script type="text/javascript">{$custom_code.js nofilter}</script>
{/if}


