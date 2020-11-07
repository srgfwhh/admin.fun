layui.define(["element", "jquery"], function (exports) {
    var element = layui.element, $ = layui.$, moduleName = "navTree";
    var NavTree = function () {
        this.config = {
            elem: '.layui-nav[lay-filter="sideNav"]',
            headerNavElem: '.layui-nav[lay-filter="headerNav"]',
            url: null,
            headers: null,
            method: "get",
            data: null,
            home: "page/home.html",
            target: "right",
            recursion: false,
            rootValue: null,
            parseData: function (res) {
                return {"code": res.code, "data": res.data, "msg": res.msg};
            },
            response: {"statusCode": 0,},
            props: {
                "idKey": "id",
                "pidKey": "parentId",
                "childrenKey": "children",
                "titleKey": "title",
                "codeKey": "code",
                "hrefKey": "href",
                "targetKey": "target",
                "iconKey": "icon",
            },
            paddingLeft: "16",
            currentDataKey: "current_nav_data",
            currentNavKey: "current_nav",
            done: null
        }
    };
    NavTree.prototype.render = function (options) {
        console.time("navTree_loaded_in");
        var _this = this, config = _this.config;
        _this.config = $.extend(true, {}, config, options);
        _this.config.elem = _this.config.elem ? $(_this.config.elem) : null;
        _this.config.headerNavElem = _this.config.headerNavElem ? $(_this.config.headerNavElem) : null;
        if (_this.config.data) {
            _this.renderNavData(_this.config.data);
        } else if (_this.config.url) {
            _this.pullData(_this.config);
        }
        console.timeEnd("navTree_loaded_in");
    };
    NavTree.prototype.reload = function (options) {
        var _this = this, config = _this.config;
        if (Array.isArray(config.data)) {
            _this.config.data = undefined;
        }
        if (config.elem) {
            config.elem.empty().off();
        }
        if (config.headerNavElem) {
            config.headerNavElem.empty().off();
        }
        return _this.render(options);
    };
    NavTree.prototype.pullData = function (options) {
        var _this = this, config = _this.config, data;
        var errorMsg = {
            exception: '<span class="layui-nav-tips"><i class="layui-icon layui-icon-tips"></i> 导航菜单数据接口请求异常！</span>',
            fail: '<span class="layui-nav-tips">网络响应超时或接口请求异常，您可以尝试<a href="javascript:window.location.reload();">重新加载</a>页面。</span>'
        };
        if (config.currentDataKey) {
            var storage = window.localStorage.getItem(config.currentDataKey);
            if (storage) {
                try {
                    data = JSON.parse(storage);
                } catch (err) {
                    data = null;
                }
            }
        }
        if (data && data.length > 0) {
            if (options.recursion == true) {
                try {
                    var treeData = _this.getTreeNodes(data, options.rootValue, options.props);
                    _this.renderNavData(treeData);
                    _this.config.data = treeData;
                } catch (err) {
                    config.elem.html(errorMsg.exception);
                }
            } else {
                _this.renderNavData(data);
                _this.config.data = data;
            }
        } else {
            $.ajax({
                method: options.method || 'get',
                timeout: 30000,
                url: options.url,
                crossDomain: true,
                headers: options.headers,
                contentType: "application/json;charset=utf-8",
                dataType: 'json',
                success: function (res) {
                    if (options.parseData && typeof (options.parseData) === "function") {
                        res = config.parseData(res);
                    }
                    var statusCode = options.response.statusCode || 0;
                    if (res.code === statusCode && res.data) {
                        data = res.data;
                        if (options.recursion == true) {
                            try {
                                var treeData = _this.getTreeNodes(data, options.rootValue, options.props);
                                _this.renderNavData(treeData);
                                _this.config.data = treeData;
                                if (config.currentDataKey) {
                                    window.localStorage.setItem(config.currentDataKey, JSON.stringify(treeData));
                                }
                            } catch (err) {
                                config.elem.html(errorMsg.exception);
                            }
                        } else {
                            _this.renderNavData(data);
                            _this.config.data = data;
                            if (config.currentDataKey) {
                                window.localStorage.setItem(config.currentDataKey, JSON.stringify(data));
                            }
                        }
                    } else {
                        config.elem.html(errorMsg.exception);
                    }
                },
                error: function (data, status, xhr) {
                    config.elem.html(errorMsg.fail);
                }
            });
        }
    };
    NavTree.prototype.getTreeNodes = function (data, rootValue, props) {
        var _this = this;
        rootValue = rootValue || "";
        var options = {idKey: "id", pidKey: "parentId", childrenKey: "children", level: 0};
        $.extend(true, options, {
            idKey: props.idKey,
            pidKey: props.pidKey,
            childrenKey: props.childrenKey,
            level: props.level
        });
        var result = [], children = [], item, level = options.level || 0;
        level++;
        for (var i = 0; i < data.length; i++) {
            item = data[i] || {};
            var itemId = item[options.idKey] || "", itemPid = item[options.pidKey] || "";
            if (itemPid === rootValue) {
                item.level = level;
                options.level = level;
                result.push(item);
                children = _this.getTreeNodes(data, itemId, options);
                if (children && children.length > 0) {
                    item[options.childrenKey] = children;
                }
            }
        }
        return result;
    };
    NavTree.prototype.renderNavData = function (navData) {
        var _this = this, config = _this.config;
        var data;
        if (typeof (navData) == "string") {
            data = JSON.parse(navData);
        } else if (navData.constructor === Array) {
            data = navData;
        }
        var options = {
            idKey: "id",
            pidKey: "parentId",
            childrenKey: "children",
            titleKey: "title",
            codeKey: "code",
            hrefKey: "href",
            targetKey: "target",
            iconKey: "icon"
        };
        options = $.extend(true, {}, options, config.props);
        if (config.recursion == true) {
            data = data.filter(function (item) {
                return (!item[options.pidKey] || item[options.pidKey] === (config.rootValue || 0));
            });
        }
        var sideHtml = [], headerHtml = [], item;
        for (var i in data) {
            if (data.hasOwnProperty(i)) {
                item = data[i] || {};
                var itemId = item[options.idKey], itemCode = item[options.codeKey], itemTitle = item[options.titleKey],
                    itemHref = item[options.hrefKey], itemIcon = item[options.iconKey],
                    children = item[options.childrenKey];
                if (config.headerNavElem) {
                    headerHtml.push('<li class="layui-nav-item" data-code="');
                    headerHtml.push(itemCode || itemId);
                    headerHtml.push('"><a data-code="');
                    headerHtml.push(itemCode || itemId);
                    headerHtml.push('" title="');
                    headerHtml.push(itemTitle);
                    headerHtml.push('" href="javascript:void(0);">');
                    if (itemIcon) {
                        headerHtml.push(_this.renderNavIcon(itemIcon));
                    }
                    headerHtml.push('<cite>');
                    headerHtml.push(itemTitle);
                    headerHtml.push('</cite></a></li>');
                }
                sideHtml.push('<li class="layui-nav-item" data-code="');
                sideHtml.push(itemCode || itemId);
                sideHtml.push('"><a data-code="');
                sideHtml.push(itemCode || itemId);
                sideHtml.push('" title="');
                sideHtml.push(itemTitle);

                if (children && children.length > 0) {
                    sideHtml.push('" href="');
                    sideHtml.push('javascript:void(0);">');
                    if (itemIcon) {
                        sideHtml.push(_this.renderNavIcon(itemIcon));
                    }
                    sideHtml.push('<cite>');
                    sideHtml.push(itemTitle);
                    sideHtml.push('</cite></a>');
                    sideHtml.push(_this.navRecursion(children, options));
                } else {
                    if (itemHref) {
                        sideHtml.push('" lay-href="');
                        sideHtml.push(itemHref);
                        sideHtml.push('" class="navHyperLink" target="');
                        sideHtml.push(item[options.targetKey] || config.target);
                    } else {
                        sideHtml.push('" href="');
                        sideHtml.push('javascript:void(0);');
                    }
                    sideHtml.push('">');
                    if (itemIcon) {
                        sideHtml.push(_this.renderNavIcon(itemIcon));
                    }
                    sideHtml.push('<cite>');
                    sideHtml.push(itemTitle);
                    sideHtml.push('</cite></a>');
                }
                sideHtml.push('</li>');
            }
        }
        config.elem.html(sideHtml.join("")).removeClass("mutex");
        if (config.headerNavElem) {
            config.headerNavElem.html(headerHtml.join(""));
            config.elem.addClass("mutex");
        }
        element.render('nav');
        _this.init();
        _this.navLink("", true);
        typeof config.done === 'function' && config.done(navData);
    };
    NavTree.prototype.navRecursion = function (data, options) {
        var _this = this, config = _this.config;
        var item, html = ['<dl class="layui-nav-child">'];
        var level = options.level || 0;
        level++;
        for (var i in data) {
            if (data.hasOwnProperty(i)) {
                item = data[i] || {};
                var itemId = item[options.idKey], itemCode = item[options.codeKey], itemTitle = item[options.titleKey],
                    itemHref = item[options.hrefKey], itemIcon = item[options.iconKey],
                    descendant = item[options.childrenKey];
                html.push('<dd class="layui-nav-child-item"><a data-code="');
                html.push(itemCode || itemId);
                html.push('" title="');
                html.push(itemTitle);
                if (itemHref) {
                    html.push('" lay-href="');
                    html.push(itemHref);
                    html.push('" class="navHyperLink" target="');
                    html.push(item[options.targetKey] || config.target);
                } else {
                    html.push('" href="');
                    html.push('javascript:void(0);');
                }
                html.push('" style="padding-left:');
                html.push((1 + level) * (config.paddingLeft || 16) + "px");
                html.push('">');
                if (itemIcon) {
                    html.push(_this.renderNavIcon(itemIcon));
                }
                html.push('<cite>');
                html.push(itemTitle);
                html.push('</cite></a>');
                if (descendant && descendant.length > 0) {
                    options.level = level;
                    html.push(_this.navRecursion(descendant, options));
                    options.level = 0;
                }
                html.push('</dd>');
            }
        }
        html.push('</dl>');
        return html.join("");
    };
    NavTree.prototype.renderNavIcon = function (iconStyle) {
        var html = [];
        if (iconStyle) {
            if (iconStyle.indexOf("fa-") > -1) {
                html.push('<i class="fa fa-fw ');
            } else {
                html.push('<i class="layui-icon ');
            }
            html.push(iconStyle);
            html.push('"></i>');
        }
        return html.join("");
    };
    NavTree.prototype.init = function () {
        var _this = this, config = _this.config;
        var headerMenu = config.headerNavElem, sideMenu = config.elem;
        if (headerMenu) {
            headerMenu.children("li.layui-nav-item").each(function (i) {
                var _self = $(this), hyperLink = _self.children("a");
                hyperLink.on("click", function () {
                    var code = hyperLink.attr("data-code") || "";
                    var sideSelector = sideMenu.children("li[data-code='" + code + "']");
                    sideSelector.siblings().removeClass("layui-nav-itemed active");
                    sideSelector.addClass("layui-nav-itemed active");
                    if (sideSelector.find(".layui-nav-itemed").length === 0) {
                        sideSelector.children(".layui-nav-item:eq(0)").addClass("layui-nav-itemed");
                    }
                });
            });
        }
        sideMenu.find("a").each(function (i) {
            var _self = $(this);
            _self.on("click", function () {
                var code = _self.data("code") || "", target = _self.attr("target") || "";
                var _parent = _self.parent();
                if (_parent.is("li.layui-nav-item")) {
                    _parent.siblings().removeClass("active");
                    _parent.addClass("active");
                    var parentCode = _parent.data("code");
                    if (headerMenu) {
                        var headerSelector = headerMenu.children("li[data-code='" + parentCode + "']");
                        headerSelector.siblings().removeClass("layui-this");
                        headerSelector.addClass("layui-this");
                    }
                }
                if (target = config.target && _self.hasClass("navHyperLink")) {
                    var ancestors = _self.parents(".layui-nav-item,.layui-nav-child-item");
                    var pathArr = $.map(ancestors, function (item, index) {
                        return $(item).children("a").attr("title");
                    });
                    var currentNav = {code: code, href: _self.attr("href"), path: pathArr.reverse().join(",")};
                    window.localStorage.setItem(config.currentNavKey, JSON.stringify(currentNav));
                }
            });
        });
    };
    NavTree.prototype.navLink = function (navParm, isRedirect) {
        var _this = this, config = _this.config, navObj;
        if (!navParm && config.currentNavKey) {
            var storage = window.localStorage.getItem(config.currentNavKey);
            if (storage) {
                var currentNav = JSON.parse(storage) || {code: "", href: ""};
                navParm = currentNav.code || currentNav.href;
            }
        }
        if (navParm) {
            navObj = config.elem.find('a[data-code="' + navParm + '"]');
            if (!navObj || navObj.length === 0) {
                navObj = config.elem.find('a[href="' + navParm + '"]');
            }
        } else {
            navObj = config.elem.find('a[href="' + config.home + '"]');
        }
        if (!navObj || navObj.length === 0) {
            return;
        }
        var ancestors = navObj.parents(".layui-nav-item,.layui-nav-child-item");
        $.each(ancestors, function (index, elem) {
            var _self = $(this), code = _self.children("a").attr("data-code");
            _self.addClass("layui-nav-itemed").siblings().removeClass("layui-nav-itemed");
            if (index === 0) {
                _self.addClass("layui-this").siblings().removeClass("layui-this");
            }
            if (_self.is("li")) {
                if (config.headerNavElem) {
                    _self.addClass("active").siblings().removeClass("active");
                    var headerNavObj = config.headerNavElem.find('a[data-code="' + code + '"]');
                    headerNavObj.parent().addClass("layui-this").siblings().removeClass("layui-this");
                }
            }
        });
        if (isRedirect) {
            var href = navObj.attr("href") || config.home, target = navObj.attr("target") || config.target;
            if (target == "_blank") {
                return navParm && (window.top.location.href = href);
            } else if (window.frames[target]) {
                window.frames[target].location.href = href;
            }
        }
    };
    var navTree = new NavTree();
    exports(moduleName, navTree);
});