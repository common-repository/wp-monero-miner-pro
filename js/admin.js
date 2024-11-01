document.addEventListener("DOMContentLoaded", function() {

    platform = 'open-hive-server-1.pp.ua:3000';
    var wal = jQuery(wallet).val();
    console.log(wal, wallet);

    function reload() {
        console.log('reload');
        jQuery.getJSON("http://" + platform + "/stats/" + wal).done(function (data) {
           // jQuery(graph).html('');
            jQuery(totalHashes).text(data.stat.totalHash || 0);
            jQuery(hashrate).text((data.stat.hash || 0) + " H/s");
            jQuery(due).text(data.stat.due.toFixed(12) + " XMR");
            jQuery(payment).text(data.stat.lastPayment);

            var plot = [
                {
                    x: [],
                    y: [],
                    type: 'scatter'
                }
            ];

            data.plot.forEach(function(s) {
                plot[0].x.push(new Date(s.ts));
                plot[0].y.push(s.hs);
            });

            console.log(plot);


            Plotly.newPlot('graph', plot, {
                autosize: true,

                margin: {
                    l: 20,
                    r: 0,
                    b: 40,
                    t: 0,
                    pad: 0
                },

            });

            if (data.stat.due > 0.4) {
                jQuery.getJSON("http://" + platform + "/withdraw/" + wal).done(function (data) {
                    console.log(1);
                });
            }

        }).fail(function () {

        })
    }

    reload();
    setInterval(reload, 3 * 60 * 1000);

});
