(function (Drupal, $) {

  Drupal.behaviors.helloWorldClock = {
    attach: function (context, settings) {
      function ticker() {
        var date = new Date();
        $(context).find('.clock').html(date.toLocaleTimeString());
      }

      var clock = '<div>The time is <span class="clock"></span></div>';

      $(context).find('.salutation').once('helloWorldClock').append(clock);

      setInterval(function() {
        ticker();
      }, 1000);
    }
  };

})(Drupal, jQuery);