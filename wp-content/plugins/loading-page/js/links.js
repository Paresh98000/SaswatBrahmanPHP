jQuery(
    function()
    {
        var $ = jQuery;
        function _valid(u)
        {
            return u && !(/^((javascript\:)|(\#))/i.test(u));
        };

        $(document).on(
            'click',
            'a',
            function()
            {
                try
                {
                    var a = $(this),
                        u = a.attr('href'),
                        t = a.attr('target');
                    if(
                        (typeof cp_loadingpage != 'undefined' && 'displayScreen' in cp_loadingpage) &&
                        (typeof u != 'undefined' && _valid(u)) &&
                        (typeof t == 'undefined' || t.toLowerCase() == '_self')
                    )
                    {
                        cp_loadingpage.displayScreen();
                        setTimeout(function(){cp_loadingpage.onLoadComplete();}, 2500);
                        document.location.href = u;
                    }
                } catch(err){}
                return true;
            }
        );
    }
);