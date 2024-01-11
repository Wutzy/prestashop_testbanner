<!-- Block testbanner -->
{if $test_banner_state == 1 && $is_user == FALSE && $current_page == 'product'}
  <div id="testbanner_block_home" class="block" style="background-color: {$test_banner_color};">
    <div class="block_content">
      <p>
        {l s='Tenus sagulis sine aut Nili pari pari sorte sine nec ab errant arva eorum seminudi.' mod='testbanner'}
      </p>
      <button id="closeBannerButton" type="button" class="grow" style="background-color: {$test_banner_color};">Close</button>
    </div>
  </div>
{/if}
<!-- /Block mymodule -->
