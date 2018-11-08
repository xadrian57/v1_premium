setTimeout(function(){ // temporario

    /*
    - -----------------------------------------------------------
    - T O U C H N I C E D - V.0.0.2 (beta) ----------------------
    - SLIDER AND CARROSSEL --------------------------------------
    - -----------------------------------------------------------
    - REQUIRES --------------------------------------------------
    - Font-Awesome //fortawesome.github.io/Font-Awesome ---------
    - for icons -------------------------------------------------
    - -----------------------------------------------------------
    - Created: 03/02/2018 ---------------------------------------
    - Updated: 06/02/2018 ---------------------------------------
    - -----------------------------------------------------------
    - ROI HERO --------------------------------------------------
    - https://www.roihero.com.br --------------------------------
    - -----------------------------------------------------------
    - Courtesy --------------------------------------------------
    - WIZARD FLY ------------------------------------------------
    - Adonis Vieira - Analist Front End -------------------------
    - http://wfly.esy.es ----------------------------------------
    - -----------------------------------------------------------
    - I T I S T H E G R E A T E ( w ) I Z A R D W H O F L I E S -
    - -----------------------------------------------------------
    */

    // W I Z A R D
    // A P P L I C A T I O N
    var Wapp = Wapp || {};

    // M O D U L E
    Wapp.TouchNiced = {};

    (function(doc, win, vars) {
        'use strict';

        vars = {
            sld: 'touchNiced',
            block: 'blockNiced',
            content: 'contentNiced',
            loader: 'loaderNiced',
            view: win.innerWidth, // default width
            transition: 0.6, // default transition
            init: false,
            device: 0, // click support [0] | touch support [1]

            // positions
            tscX: 0, // px
            tscY: 0, // px
            tmcX: 0, // px
            tmcY: 0, // px
            tedX: 0, // px
            tedY: 0, // px

            // events
            startEvent: '',
            moveEvent: '',
            releaseEvent: '',
            outEvent: 'mouseout',
        };

        // ----
        // Init
        // ----
        // for all functions init
        Wapp.TouchNiced.Init = function() {
            //console.log('-- T O U C H N I C E D - V.0.0.2 (beta) -- [init]');

            Wapp.TouchNiced.Listen();
            Wapp.TouchNiced.CreateSld();

            // not add event repeat
            vars.init = true;
        };

        // ------
        // Listen
        // ------
        // for all events listener
        Wapp.TouchNiced.Listen = function() {
            win.addEventListener('resize', Wapp.TouchNiced.CreateSld);

            // vars.startEvent = Wapp.TouchNiced.CheckEvent() ? 'touchstart' : 'mousedown';
            // vars.moveEvent = Wapp.TouchNiced.CheckEvent() ? 'touchmove' : 'mousemove';
            // vars.releaseEvent = Wapp.TouchNiced.CheckEvent() ? 'touchend' : 'mouseup';

            vars.startEvent = 'mousedown';
            vars.moveEvent = 'mousemove';
            vars.releaseEvent = 'mouseup';
        };

        // ---------
        // AddEvents
        // ---------
        // add events for listeners
        Wapp.TouchNiced.AddEvents = function(el) {
            // el.addEventListener(vars.startEvent, Wapp.TouchNiced.TouchStart, true);
            // el.addEventListener(vars.releaseEvent, Wapp.TouchNiced.TouchEnd, true);
            // el.addEventListener(vars.outEvent, Wapp.TouchNiced.TouchEnd, true);
        };

        // ----------
        // CheckEvent
        // ----------
        // check device for correct addEventListener
        Wapp.TouchNiced.CheckEvent = function() {
            // touch support
            if ('ontouchstart' in window) {
              vars.device = 1;
              return true;

              // click support
            } else {
              vars.device = 0;
              return false;
            }
        };

        // -----------
        // PauseEvents
        // -----------
        // remove events - default
        Wapp.TouchNiced.PauseEvents = function(e) {
            if (e.stopPropagation) e.stopPropagation();
            e.returnValue = false;

            return false;
        };

        // ----------
        // TouchStart
        // ----------
        // get start position [event] in element (slide) pressed
        Wapp.TouchNiced.TouchStart = function(e) {
            var
                el = Wapp.TouchNiced.FindParent(e.target, 'touchNiced');

            vars.tscX = Wapp.TouchNiced.CheckEvent() ? parseInt(e.touches[0].clientX) : parseInt(e.clientX);
            vars.tscY = Wapp.TouchNiced.CheckEvent() ? parseInt(e.touches[0].clientY) : parseInt(e.clientY);
            vars.tedX = 0;
            vars.tedY = 0;

            el.addEventListener(vars.moveEvent, Wapp.TouchNiced.TouchMove, true);
        };

        // ---------
        // TouchMove
        // ---------
        // get move position [event] in element (slide)
        Wapp.TouchNiced.TouchMove = function(e) {
            vars.tedX = Wapp.TouchNiced.CheckEvent() ? parseInt(e.changedTouches[0].clientX - vars.tscX) : parseInt(e.clientX - vars.tscX);
            vars.tedY = Wapp.TouchNiced.CheckEvent() ? parseInt(e.changedTouches[0].clientY - vars.tscY) : parseInt(e.clientY - vars.tscY);

            Wapp.TouchNiced.CheckMove(e, vars.tedX, vars.tedY);
            Wapp.TouchNiced.PauseEvents(e);
        };

        // --------
        // TouchEnd
        // --------
        // get end position [event] in element (slide)
        // if not have moviment [vars.tedX] in TouchMove, window.location this href [vars.url]
        Wapp.TouchNiced.TouchEnd = function(e) {
            var
              el = Wapp.TouchNiced.FindParent(e.target, 'touchNiced');

            el.removeEventListener(vars.moveEvent, Wapp.TouchNiced.TouchMove, true);

        };

        // ---------
        // CheckMove
        // ---------
        // check position [left or right] for changes (slide)
        Wapp.TouchNiced.CheckMove = function(e, posX, posY) {
            var
              el = Wapp.TouchNiced.FindParent(e.target, 'touchNiced');

            if (posY !== 0) {
              el.removeEventListener(vars.moveEvent, Wapp.TouchNiced.TouchMove, true);
              return false;
            }

            if (posX > 0) {
              Wapp.TouchNiced.Prev(e);

            } else {
              Wapp.TouchNiced.Next(e);
            }

            Wapp.TouchNiced.PauseEvents(e);
            Wapp.TouchNiced.TouchEnd(e);
        };

        // ---------
        // FindParent
        // ---------
        // get parent element
        Wapp.TouchNiced.FindParent = function(el, cls) {
            while ((el = el.parentElement) && !el.classList.contains(cls));
            return el;
        };

        // ----
        // Prev
        // ----
        // event left slider
        Wapp.TouchNiced.Prev = function(e) {
            var
                target = e.target,
                sld = Wapp.TouchNiced.FindParent(target, 'touchNiced'),
                transition = sld.dataset.transition || vars.transition,
                base = sld.children[0].children[0],
                list = base.children[1],
                first = list.children[0],
                last = list.children[list.children.length - 1],
                copyLast = last.cloneNode(true),
                margin = parseFloat(win.getComputedStyle(base).marginLeft.replace('px', ''));

            base.style.transition = 'margin ' + transition + 's';
            base.style.marginLeft = 0;

            target.removeEventListener('click', Wapp.TouchNiced.Prev, true);

            setTimeout(function() {
                base.style.transition = 'none';
                last.remove();
                list.insertBefore(copyLast, first);
                base.style.marginLeft = margin + 'px';
                target.addEventListener('click', Wapp.TouchNiced.Prev, true);
            }, transition * 1000);
        };

        // ----
        // Next
        // ----
        // event next slider
        Wapp.TouchNiced.Next = function(e) {
            var
                target = e.target,
                sld = Wapp.TouchNiced.FindParent(target, 'touchNiced'),
                transition = sld.dataset.transition || vars.transition,
                base = sld.children[0].children[0],
                list = base.children[1],
                first = list.children[0],
                last = list.children[list.children.length - 1],
                copyFirst = first.cloneNode(true),
                margin = parseFloat(win.getComputedStyle(base).marginLeft.replace('px', '')) * 2;

            base.style.transition = 'margin ' + transition + 's';
            base.style.marginLeft = margin + 'px';

            target.removeEventListener('click', Wapp.TouchNiced.Next, true);

            setTimeout(function() {
                base.style.transition = 'none';
                first.remove();
                list.insertBefore(copyFirst, last);
                base.style.marginLeft = parseFloat(margin / 2) + 'px';
                target.addEventListener('click', Wapp.TouchNiced.Next, true);
            }, transition * 1000);
        };

        // ---------
        // CreateSld
        // ---------
        // create slider and set attributes (data-attr)
        // read # config in HEADER
        // --
        Wapp.TouchNiced.CreateSld = function() {
            var
                x = 0,
                y = 0,
                el = '',
                els = doc.getElementsByClassName(vars.sld),
                base = '',
                div = '',
                btn = '',
                slides = '',
                child = '',
                margin = 0,
                list = '',
                copy = '',
                width = 0,
                responsa = '';

            // for all sliders
            for (x = 0; x < els.length; x++) {
                el = els[x];
                base = el.getElementsByClassName(vars.block)[0];
                list = base.children[1];
                margin = el.dataset.margin || 0;
                responsa = el.dataset.responsa;
                slides = base.getElementsByTagName('li');

                //Wapp.TouchNiced.AddEvents(el);

                // - - - - - -
                // c o n f i g
                // - - - - - -

                // grid width
                if (el.dataset.grid) {
                    vars.view = parseFloat(doc.getElementsByClassName(el.dataset.grid.replace('.', ''))[0].offsetWidth - 60);
                    //console.log('vars.view', vars.view);
                }

                // for resize
                // init [show all slides]
                if (!vars.init) {
                    if ((el.dataset.init) && (!el.dataset.timeout)) {
                        Wapp.TouchNiced.AddClassTime(el, 'showNiced');
                    }

                    // timeout [show all slides by time]
                    if (el.dataset.timeout) {
                        Wapp.TouchNiced.AddClassTime(el, 'showNiced', parseInt(el.dataset.timeout));
                    }

                    // create navigation
                    if (el.dataset.nav) {
                        div = doc.createElement('div');
                        div.className = 'navigatorNiced';

                        // left [PREV]
                        btn = doc.createElement('i');
                        btn.dataset.event = 'prev';
                        btn.addEventListener('click', Wapp.TouchNiced.Prev, true);

                        if (el.dataset.left) {
                            btn.className = el.dataset.left;

                        } else {
                            div.classList.add('noIcon');
                        }

                        div.appendChild(btn);

                        // right [NEXT]
                        btn = doc.createElement('i');
                        btn.dataset.event = 'next';
                        btn.addEventListener('click', Wapp.TouchNiced.Next, true);

                        if (el.dataset.right) {
                            btn.className = el.dataset.right;

                        } else {
                        div.classList.add('noIcon');
                        }

                        div.appendChild(btn);
                        el.appendChild(div);
                    }
                }

            width = parseFloat((vars.view / el.dataset.view) - (margin * 2));

            // responsive
            if (responsa) {
                if (Wapp.TouchNiced.GetWidth(JSON.parse(responsa), margin)) {
                    width = Wapp.TouchNiced.GetWidth(JSON.parse(responsa), margin);
                    //console.log('width responsa', width);
                }
            }

            // iteration slides
            for (y = 0; y < slides.length; y++) {
                // for resize
                if (!vars.init) {
                    // create caption
                    if (el.dataset.caption) {
                        div = doc.createElement('div');
                        div.className = 'captionNiced';
                        div.textContent = slides[y].getElementsByTagName('img')[0].alt;

                        slides[y].appendChild(div);
                    }
                }

                // set width
                slides[y].style.width = width + 'px';
                slides[y].style.marginLeft = margin + 'px';
                slides[y].style.marginRight = margin + 'px';
            }

            // width base
            base.style.width = (vars.view * slides.length) + 'px';

            // margin base
            base.style.marginLeft = '-' + (width + (margin * 2)) + 'px';

            // for resize
            if (!vars.init) {
                // get last slide and insert before first
                // for PREV
                child = list.children[list.children.length - 1];
                copy = child.cloneNode(true);
                child.remove();

                list.insertBefore(copy, list.childNodes[0]);
            }
        }
        };

        // --------
        // GetWidth
        // --------
        // for responsive grid
        Wapp.TouchNiced.GetWidth = function(obj, margin) {
            var
                key = '';

            for (key in obj) {
                // console.log('key', key);
                // console.log('obj[key]', obj[key]);
                if (vars.view < parseInt(key, 0)) {
                    //console.log('return false', parseFloat((vars.view / parseInt(obj[key])) - (margin * 2)));
                    return parseFloat((vars.view / parseInt(obj[key])) - (margin * 2));
                }
            }
        };

        // ------------
        // AddClassTime
        // ------------
        // add class in element by id and check init
        Wapp.TouchNiced.AddClassTime = function(el, className, time) {
            if (time) {
                setTimeout(function() {
                    el.classList.add(className);
                }, time);

            } else {
                el.classList.add(className);
            }
        };

        // --------
        // LETS GO!
        // --------
        // init all functions
        doc.addEventListener('DOMContentLoaded', Wapp.TouchNiced.Init({debug: true}));
    }(document, window, 'Private'));

    /*
    - -----------------------------------------------------------
    - T E M P L A T E S F U N C T I O N S -----------------------
    - all functions for template --------------------------------
    - -----------------------------------------------------------
    - Created: 06/02/2018 ---------------------------------------
    - Updated: 06/02/2018 ---------------------------------------
    - -----------------------------------------------------------
    - ROI HERO --------------------------------------------------
    - https://www.roihero.com.br --------------------------------
    - -----------------------------------------------------------
    - Courtesy --------------------------------------------------
    - WIZARD FLY ------------------------------------------------
    - Adonis Vieira - Analist Front End -------------------------
    - http://wfly.esy.es ----------------------------------------
    - -----------------------------------------------------------
    - I T I S T H E G R E A T E ( w ) I Z A R D W H O F L I E S -
    - -----------------------------------------------------------
    */

    // W I Z A R D
    // A P P L I C A T I O N
    var Wapp = Wapp || {};

    // M O D U L E
    Wapp.TplFnc = {};

    (function(doc, win, vars) {
        'use strict';

        vars = {
            open            : doc.getElementsByClassName('rh_tpl_kit_offerslimited_open')[0],
            close           : doc.getElementsByClassName('rh_tpl_kit_offerslimited_close')[0],
            closeOverlay    : doc.getElementsByClassName('rh_tpl_close')[0],
        };

        Wapp.TplFnc.Init = function() {
            //console.log('-- T E M P L A T E S F U N C T I O N S -- [init]');

            Wapp.TplFnc.Listen();
        };

        Wapp.TplFnc.Listen = function() {
            if (vars.open){
                vars.open.addEventListener('click', Wapp.TplFnc.Sidebar, true);
                vars.close.addEventListener('click', Wapp.TplFnc.Sidebar, true);
            }        

            if(vars.closeOverlay){
                vars.closeOverlay.addEventListener('click', Wapp.TplFnc.Overlay, true);
            }
        };

        Wapp.TplFnc.Sidebar = function(e) {
            var
                target = e.target,
                box = target.nextElementSibling;

            if (target.classList.contains('rh_tpl_kit_offerslimited_close')) {
                box = target.parentElement;
            }

            if (box.classList.contains('active')) {
                box.classList.remove('active');
                vars.open.classList.remove('active');

            } else {
                box.classList.add('active');
                vars.open.classList.add('active');
            }

            e.preventDefault();

            return false;
        };

        Wapp.TplFnc.Overlay = function(e) {
            var
                target = e.target,
                box = document.getElementsByClassName('gridOverlay')[0];

            box.classList.remove('active');

            box.style.opacity = '0';
            setTimeout(function(){  
                box.style.display = 'none';         
            },500);

            e.preventDefault();

            return false;
        };

        var rhCheckOL = function(){
            if (document.getElementsByClassName('rh_tpl_kit_offerslimited')[0]){            
                setTimeout(function(){
                    Wapp.TplFnc.Init();
                },200);
            } else {
                setTimeout(function(){
                    rhCheckOL();
                },200);
            }
        }
        rhCheckOL();
    }(document, window, 'Private'));

    // overlay eliabe
    (function(){
        window['rh-overlay'] = function(){
            var overlay = document.getElementsByClassName('gridOverlay')[0];
            if (overlay){
                window['rh_lite_mostrou'] = 0;
                function rhAddEvent(obj, evt, fn) {
                    if (obj.addEventListener) {
                        obj.addEventListener(evt, fn, false);
                    }
                    else if (obj.attachEvent) {
                        obj.attachEvent("on" + evt, fn);
                    }
                }
                rhAddEvent(document, "mouseout", function(e) {
                    e = e ? e : window.event;
                    if (e.clientY < 0){ // CASO O MOUSE ESTEJA ACIMA DO DOCUMENTO HTML
                        var from = e.relatedTarget || e.toElement;
                        if (!from || from.nodeName == "HTML") {
                            // stop your drag event here
                            // for now we can just use an alert
                            if (window['rh_lite_mostrou'] == 0){
                                overlay.style.display = 'block';
                                setTimeout(function(){
                                    Wapp.TouchNiced.CreateSld();//ajusta slider
                                    overlay.style.opacity = '1';
                                },200);
                                    
                                window['rh_lite_mostrou'] = 1;



                                // SETANDO COOKIE
                                var rh_lite_agora = new Date();
                                rh_lite_agora.setHours(rh_lite_agora.getHours() + 3);
                                document.cookie = "rhOverlay=1; expires=" + rh_lite_agora.toUTCString() + "; path=/";
                            }
                            return false;
                        }
                    }
                });
            }   
        }

        var rhCheckOV = function(){
            if (document.getElementsByClassName('gridOverlay')[0]){
                window['rh-overlay']();
                Wapp.TplFnc.Listen();
            } else {
                setTimeout(function(){
                    rhCheckOV();
                },200);
            }
        }   
        rhCheckOV();
    }());

    // fixed footer
    (function(){
        var container = document.getElementsByClassName('rh_tpl_fixed_footer')[0];
        if(container){
            // document
            document.addEventListener('scroll',function(e){
                var 
                    doc = document.documentElement,
                    left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0),
                    top = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0),
                    body = document.body, html = document.documentElement,
                    height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );

                    // console.log('ALTURA DOCUMENTO: '+height);
                    // console.log('DISTANCIA DO TOPO: '+top);
                    // console.log('(altura / 100 * 40) + topo = '+(height / 100 * 40) + top);

                if ((height / 100 * 50) + top >= height){ // QUANDO CHEGAR PERTO DO FINAL, MOSTRAR O OVERLAY    
                    container.style.display = 'block';
                    Wapp.TouchNiced.CreateSld();
                    setTimeout(function(){
                        container.style.opacity = '1';
                    },200);
                } else {
                    container.style.display = 'none';
                    container.style.opacity = '0';
                }
            });
            // butao fechar
            var btnClose = container.getElementsByClassName('btn_close')[0];
            btnClose.addEventListener('click',function(){
                container.style.height = container.offsetHeight + 'px'; // atribui uma altura para a animação funcionar
                setTimeout(function(){
                    container.style.height = 0;
                },50);
            });
        }
    }())

},1500);

