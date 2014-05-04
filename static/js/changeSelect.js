(function($){
    $.fn.changeSelect = function(options){
        var defaults = {
            'width':'150px',
            'height':"20px",
            'count':8,
            'data':[]
        };
        var options = $.extend(defaults,options);
        return this.each(function(){
            var _SELF = $(this);
            _SELF.css({
                'width':options['width'],
                "position":'relative'
            });
            var $input = _SELF.find('input[type=text]'),
                $hide = _SELF.find('input[type=hidden]'),
                $div = _SELF.children('div'),
                $list = _SELF.find('ul');
            $input.css({
                'width':options['width'],
                'height':options['height']
            });
            var data = options['data'];
            $list.html('');
            for (i=0;i<data.length;i++) {
                var list;
                list = "<li itemid = "+data[i]['itemid']+">" + data[i]['itemname'] + "</li>";
                $list.append(list);
            }
            $list.css({
                "width":options['width'],
                "position":"absolute",
                "top":options['height'],
                "left":0,
                "border-width":'1px',
                "border-color":'#ccc',
                "border-style":'solid',
                "margin":0,
                "list-style":'none',
                "line-height":'24px',
                "max-height":24*options['count'] + 'px',
                "overflow-x":'hidden',
                "overflow-y":"auto",
                'text-indent':'0.25em',
                'z-index': 100000
            });
            $input.focus(function(){$list.show();})
            $input.keyup(function(){
                var key = $(this).val();
                selectWord(key);
            });
            $list.find('li').click(function(){
                var name = $(this).html();
                var list = $input.parent('span').parent('div').siblings().children('span').find('input[type=text]');
                var listarr = new Array();
                for (i=0;i<list.length;i++) {
                    if (name == list.eq(i).val()) {
                        alert(name+' 已存在');
                        return;
                    }
                }
                $input.val($(this).html());
                $hide.val($(this).attr('itemid'));
                $list.hide();
            })
            $(document).click(function(e){
                var target = $(e.target);
                if (target.closest(_SELF).length == 0) {
                    $list.hide();
                    $input.blur(function(){
                        var itemname = $(this).val();
                        data.forEach(function(e){
                            if (itemname == e['itemname']) {
                                $hide.val(e['itemid']);
                                return;
                            }
                        })
                        
                    }); 
                }
            })
            $list.find('li').hover(function(){
                $(this).css({
                    'cursor':'pointer',
                    'background-color':'#0088cc',
                    'color':'#fff'
                });
            },function(){
                $(this).css({
                    'color':'#000',
                    'background-color':'#fff'
                })
            })
            
            function selectWord(key) {
                var list = new Array();
                for (i=0;i<$list.find('li').length;i++) {
                    list[i] = $list.find('li').eq(i);
                }
                list.forEach(function(li){
                    var show = li.html().indexOf(key);
                    if (show>-1) {
                        li.show();
                    }else{
                        li.hide();
                    }
                })
            }
        })
    }
})(jQuery)