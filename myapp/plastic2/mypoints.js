webpackJsonp([33],{29:function(module,exports){eval('"use strict";\n\nmodule.exports = {\n\tdata: function data() {\n\t\treturn {\n\t\t\tproducts: "",\n\t\t\tpoints: 0\n\t\t};\n\t},\n\tmounted: function mounted() {\n\t\tvar _this = this;\n\t\ttry {\n\t\t\tvar piwikTracker = Piwik.getTracker("http://wa.myplas.com/piwik.php", 2);\n\t\t\tpiwikTracker.trackPageView();\n\t\t} catch (err) {}\n\t\t$.ajax({\n\t\t\ttype: "get",\n\t\t\turl: "/api/qapi1/getProductList",\n\t\t\tdata: {\n\t\t\t\ttoken: window.localStorage.getItem("token"),\n\t\t\t\tpage: 1,\n\t\t\t\tsize: 10\n\t\t\t},\n\t\t\tdataType: \'JSON\'\n\t\t}).then(function (res) {\n\t\t\tconsole.log(res);\n\t\t\tif (res.err == 1) {\n\t\t\t\tmui.alert("", res.msg, function () {\n\t\t\t\t\t_this.$router.push({ name: \'login\' });\n\t\t\t\t});\n\t\t\t} else {\n\t\t\t\t_this.products = res.info;\n\t\t\t\t_this.points = res.pointsAll;\n\t\t\t}\n\t\t}, function () {});\n\t}\n};//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vbXlwb2ludHMudnVlPzZhN2UiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7QUEyQkE7dUJBRUE7O2FBRUE7V0FFQTtBQUhBO0FBSUE7NkJBQ0E7Y0FDQTtNQUNBO3lFQUNBO2dCQUNBO2dCQUVBLENBQ0E7O1NBRUE7UUFDQTs7dUNBRUE7VUFDQTtVQUVBO0FBSkE7YUFLQTtBQVJBLHlCQVNBO2VBQ0E7cUJBQ0E7dUNBQ0E7Z0NBQ0E7QUFDQTtVQUNBO3lCQUNBO3VCQUNBO0FBQ0E7aUJBRUEsQ0FDQTtBQUNBO0FBckNBIiwiZmlsZSI6IjI5LmpzIiwic291cmNlc0NvbnRlbnQiOlsiPHRlbXBsYXRlPlxuPGRpdj5cbjxoZWFkZXIgaWQ9XCJiaWdDdXN0b21lckhlYWRlclwiPlxuXHQ8YSBjbGFzcz1cImJhY2tcIiBocmVmPVwiamF2YXNjcmlwdDp3aW5kb3cuaGlzdG9yeS5iYWNrKCk7XCI+PC9hPlxuXHTmiJHnmoTnp6/liIZcbjwvaGVhZGVyPlxuPGRpdiBjbGFzcz1cIm15cG9pbnRzXCI+XG5cdDxpIGNsYXNzPVwiaWNvbnBvaW50c1wiPjwvaT48c3BhbiBjbGFzcz1cInBvaW50c1wiPuaIkeeahOenr+WIhjwvc3Bhbj48c3BhbiBjbGFzcz1cInBvaW50c251bVwiPnt7cG9pbnRzfX08L3NwYW4+PGJyPlxuXHQ8cm91dGVyLWxpbmsgOnRvPVwie25hbWU6J3BvaW50c3JlY29yZCd9XCI+5YWR5o2i6K6w5b2VPC9yb3V0ZXItbGluaz5cblx0PHJvdXRlci1saW5rIDp0bz1cIntuYW1lOidwb2ludHNydWxlJ31cIj7np6/liIbop4TliJk8L3JvdXRlci1saW5rPlxuXHQ8cm91dGVyLWxpbmsgOnRvPVwie25hbWU6J3BvaW50c2RldGFpbCd9XCI+56ev5YiG5piO57uGPC9yb3V0ZXItbGluaz5cbjwvZGl2PlxuPGgzIGNsYXNzPVwicG9pbnRzdGl0bGVcIj5cblx05ZWG5ZOB5o6o6I2QXG48L2gzPlxuPHVsIGlkPVwicHJvZHVjdFVsXCI+XG5cdDxsaSB2LWZvcj1cInAgaW4gcHJvZHVjdHNcIj5cblx0XHQ8cm91dGVyLWxpbmsgOnRvPVwie25hbWU6J3Byb2R1Y3RkZXRhaWwnLHBhcmFtczp7aWQ6cC5pZH19XCI+XG5cdFx0XHQ8aW1nIHYtYmluZDpzcmM9XCJwLnRodW1iXCI+XG5cdFx0XHQ8aDM+e3twLm5hbWV9fTwvaDM+XG5cdFx0XHQ8cD48c3BhbiBjbGFzcz1cInJlZFwiPnt7cC5wb2ludHN9fTwvc3Bhbj7np6/liIY8L3A+XG5cdFx0PC9yb3V0ZXItbGluaz5cblx0PC9saT5cbjwvdWw+XG48L2Rpdj5cbjwvdGVtcGxhdGU+XG48c2NyaXB0PlxubW9kdWxlLmV4cG9ydHMgPSB7XG5cdGRhdGE6IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiB7XG5cdFx0XHRwcm9kdWN0czpcIlwiLFxuXHRcdFx0cG9pbnRzOjBcblx0XHR9XG5cdH0sXG5cdG1vdW50ZWQ6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciBfdGhpcyA9IHRoaXM7XG5cdFx0XHR0cnkge1xuXHQgICAgdmFyIHBpd2lrVHJhY2tlciA9IFBpd2lrLmdldFRyYWNrZXIoXCJodHRwOi8vd2EubXlwbGFzLmNvbS9waXdpay5waHBcIiwgMik7XG5cdCAgICBwaXdpa1RyYWNrZXIudHJhY2tQYWdlVmlldygpO1xuXHR9IGNhdGNoKCBlcnIgKSB7XG5cdFx0XG5cdH1cblx0XHQkLmFqYXgoe1xuICAgIFx0XHR0eXBlOlwiZ2V0XCIsXG4gICAgXHRcdHVybDpcIi9hcGkvcWFwaTEvZ2V0UHJvZHVjdExpc3RcIixcbiAgICBcdFx0ZGF0YTp7XG4gICAgXHRcdFx0dG9rZW46IHdpbmRvdy5sb2NhbFN0b3JhZ2UuZ2V0SXRlbShcInRva2VuXCIpLFxuICAgIFx0XHRcdHBhZ2U6MSxcbiAgICBcdFx0XHRzaXplOjEwXG4gICAgXHRcdH0sXG4gICAgXHRcdGRhdGFUeXBlOiAnSlNPTidcbiAgICBcdH0pLnRoZW4oZnVuY3Rpb24ocmVzKXtcbiAgICBcdFx0Y29uc29sZS5sb2cocmVzKTtcblx0XHQgICAgaWYocmVzLmVycj09MSl7XG5cdFx0XHRcdG11aS5hbGVydChcIlwiLHJlcy5tc2csZnVuY3Rpb24oKXtcblx0XHRcdFx0XHRfdGhpcy4kcm91dGVyLnB1c2goeyBuYW1lOiAnbG9naW4nIH0pO1xuXHRcdFx0XHR9KTsgICAgICAgIFx0XHRcdFx0XHRcblx0XHRcdH1lbHNle1xuXHRcdFx0XHRfdGhpcy5wcm9kdWN0cz1yZXMuaW5mbztcblx0XHRcdFx0X3RoaXMucG9pbnRzPXJlcy5wb2ludHNBbGw7XG5cdFx0XHR9XG4gICAgXHR9LGZ1bmN0aW9uKCl7XG4gICAgXHRcdFxuICAgIFx0fSk7XHRcblx0fVxufVxuPC9zY3JpcHQ+XG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIG15cG9pbnRzLnZ1ZT83YjgwNzgxYSJdLCJzb3VyY2VSb290IjoiIn0=')},71:function(module,exports,__webpack_require__){eval('var __vue_exports__, __vue_options__\nvar __vue_styles__ = {}\n\n/* script */\n__vue_exports__ = __webpack_require__(29)\n\n/* template */\nvar __vue_template__ = __webpack_require__(97)\n__vue_options__ = __vue_exports__ = __vue_exports__ || {}\nif (\n  typeof __vue_exports__.default === "object" ||\n  typeof __vue_exports__.default === "function"\n) {\n__vue_options__ = __vue_exports__ = __vue_exports__.default\n}\nif (typeof __vue_options__ === "function") {\n  __vue_options__ = __vue_options__.options\n}\n\n__vue_options__.render = __vue_template__.render\n__vue_options__.staticRenderFns = __vue_template__.staticRenderFns\n\nmodule.exports = __vue_exports__\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdmlld3MvbXlwb2ludHMudnVlPzgwNDMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBIiwiZmlsZSI6IjcxLmpzIiwic291cmNlc0NvbnRlbnQiOlsidmFyIF9fdnVlX2V4cG9ydHNfXywgX192dWVfb3B0aW9uc19fXG52YXIgX192dWVfc3R5bGVzX18gPSB7fVxuXG4vKiBzY3JpcHQgKi9cbl9fdnVlX2V4cG9ydHNfXyA9IHJlcXVpcmUoXCIhIWJhYmVsLWxvYWRlciF2dWUtbG9hZGVyL2xpYi9zZWxlY3Rvcj90eXBlPXNjcmlwdCZpbmRleD0wIS4vbXlwb2ludHMudnVlXCIpXG5cbi8qIHRlbXBsYXRlICovXG52YXIgX192dWVfdGVtcGxhdGVfXyA9IHJlcXVpcmUoXCIhIXZ1ZS1sb2FkZXIvbGliL3RlbXBsYXRlLWNvbXBpbGVyP2lkPWRhdGEtdi0xNTk1YmJiMCF2dWUtbG9hZGVyL2xpYi9zZWxlY3Rvcj90eXBlPXRlbXBsYXRlJmluZGV4PTAhLi9teXBvaW50cy52dWVcIilcbl9fdnVlX29wdGlvbnNfXyA9IF9fdnVlX2V4cG9ydHNfXyA9IF9fdnVlX2V4cG9ydHNfXyB8fCB7fVxuaWYgKFxuICB0eXBlb2YgX192dWVfZXhwb3J0c19fLmRlZmF1bHQgPT09IFwib2JqZWN0XCIgfHxcbiAgdHlwZW9mIF9fdnVlX2V4cG9ydHNfXy5kZWZhdWx0ID09PSBcImZ1bmN0aW9uXCJcbikge1xuX192dWVfb3B0aW9uc19fID0gX192dWVfZXhwb3J0c19fID0gX192dWVfZXhwb3J0c19fLmRlZmF1bHRcbn1cbmlmICh0eXBlb2YgX192dWVfb3B0aW9uc19fID09PSBcImZ1bmN0aW9uXCIpIHtcbiAgX192dWVfb3B0aW9uc19fID0gX192dWVfb3B0aW9uc19fLm9wdGlvbnNcbn1cblxuX192dWVfb3B0aW9uc19fLnJlbmRlciA9IF9fdnVlX3RlbXBsYXRlX18ucmVuZGVyXG5fX3Z1ZV9vcHRpb25zX18uc3RhdGljUmVuZGVyRm5zID0gX192dWVfdGVtcGxhdGVfXy5zdGF0aWNSZW5kZXJGbnNcblxubW9kdWxlLmV4cG9ydHMgPSBfX3Z1ZV9leHBvcnRzX19cblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vc3JjL3ZpZXdzL215cG9pbnRzLnZ1ZVxuLy8gbW9kdWxlIGlkID0gNzFcbi8vIG1vZHVsZSBjaHVua3MgPSAzMyJdLCJzb3VyY2VSb290IjoiIn0=')},97:function(module,exports){eval('module.exports={render:function (){var _vm=this;var _h=_vm.$createElement;\n  return _h(\'div\', [_vm._m(0), " ", _h(\'div\', {\n    staticClass: "mypoints"\n  }, [_h(\'i\', {\n    staticClass: "iconpoints"\n  }), _h(\'span\', {\n    staticClass: "points"\n  }, ["我的积分"]), _h(\'span\', {\n    staticClass: "pointsnum"\n  }, [_vm._s(_vm.points)]), _h(\'br\'), " ", _h(\'router-link\', {\n    attrs: {\n      "to": {\n        name: \'pointsrecord\'\n      }\n    }\n  }, ["兑换记录"]), " ", _h(\'router-link\', {\n    attrs: {\n      "to": {\n        name: \'pointsrule\'\n      }\n    }\n  }, ["积分规则"]), " ", _h(\'router-link\', {\n    attrs: {\n      "to": {\n        name: \'pointsdetail\'\n      }\n    }\n  }, ["积分明细"])]), " ", _h(\'h3\', {\n    staticClass: "pointstitle"\n  }, ["\\n\\t商品推荐\\n"]), " ", _h(\'ul\', {\n    attrs: {\n      "id": "productUl"\n    }\n  }, [_vm._l((_vm.products), function(p) {\n    return _h(\'li\', [_h(\'router-link\', {\n      attrs: {\n        "to": {\n          name: \'productdetail\',\n          params: {\n            id: p.id\n          }\n        }\n      }\n    }, [_h(\'img\', {\n      attrs: {\n        "src": p.thumb\n      }\n    }), " ", _h(\'h3\', [_vm._s(p.name)]), " ", _h(\'p\', [_h(\'span\', {\n      staticClass: "red"\n    }, [_vm._s(p.points)]), "积分"])])])\n  })])])\n},staticRenderFns: [function (){var _vm=this;var _h=_vm.$createElement;\n  return _h(\'header\', {\n    attrs: {\n      "id": "bigCustomerHeader"\n    }\n  }, [_h(\'a\', {\n    staticClass: "back",\n    attrs: {\n      "href": "javascript:window.history.back();"\n    }\n  }), "\\n\\t我的积分\\n"])\n}]}//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdmlld3MvbXlwb2ludHMudnVlP2VmYWUiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEsZ0JBQWdCLG1CQUFtQixhQUFhO0FBQ2hEO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQSxHQUFHO0FBQ0g7QUFDQSxHQUFHO0FBQ0g7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsR0FBRztBQUNIO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0EsR0FBRztBQUNIO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDtBQUNBO0FBQ0E7QUFDQSxLQUFLO0FBQ0w7QUFDQSxLQUFLO0FBQ0wsR0FBRztBQUNILENBQUMsK0JBQStCLGFBQWE7QUFDN0M7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBLGdEQUFnRDtBQUNoRDtBQUNBLEdBQUc7QUFDSCxDQUFDIiwiZmlsZSI6Ijk3LmpzIiwic291cmNlc0NvbnRlbnQiOlsibW9kdWxlLmV4cG9ydHM9e3JlbmRlcjpmdW5jdGlvbiAoKXt2YXIgX3ZtPXRoaXM7dmFyIF9oPV92bS4kY3JlYXRlRWxlbWVudDtcbiAgcmV0dXJuIF9oKCdkaXYnLCBbX3ZtLl9tKDApLCBcIiBcIiwgX2goJ2RpdicsIHtcbiAgICBzdGF0aWNDbGFzczogXCJteXBvaW50c1wiXG4gIH0sIFtfaCgnaScsIHtcbiAgICBzdGF0aWNDbGFzczogXCJpY29ucG9pbnRzXCJcbiAgfSksIF9oKCdzcGFuJywge1xuICAgIHN0YXRpY0NsYXNzOiBcInBvaW50c1wiXG4gIH0sIFtcIuaIkeeahOenr+WIhlwiXSksIF9oKCdzcGFuJywge1xuICAgIHN0YXRpY0NsYXNzOiBcInBvaW50c251bVwiXG4gIH0sIFtfdm0uX3MoX3ZtLnBvaW50cyldKSwgX2goJ2JyJyksIFwiIFwiLCBfaCgncm91dGVyLWxpbmsnLCB7XG4gICAgYXR0cnM6IHtcbiAgICAgIFwidG9cIjoge1xuICAgICAgICBuYW1lOiAncG9pbnRzcmVjb3JkJ1xuICAgICAgfVxuICAgIH1cbiAgfSwgW1wi5YWR5o2i6K6w5b2VXCJdKSwgXCIgXCIsIF9oKCdyb3V0ZXItbGluaycsIHtcbiAgICBhdHRyczoge1xuICAgICAgXCJ0b1wiOiB7XG4gICAgICAgIG5hbWU6ICdwb2ludHNydWxlJ1xuICAgICAgfVxuICAgIH1cbiAgfSwgW1wi56ev5YiG6KeE5YiZXCJdKSwgXCIgXCIsIF9oKCdyb3V0ZXItbGluaycsIHtcbiAgICBhdHRyczoge1xuICAgICAgXCJ0b1wiOiB7XG4gICAgICAgIG5hbWU6ICdwb2ludHNkZXRhaWwnXG4gICAgICB9XG4gICAgfVxuICB9LCBbXCLnp6/liIbmmI7nu4ZcIl0pXSksIFwiIFwiLCBfaCgnaDMnLCB7XG4gICAgc3RhdGljQ2xhc3M6IFwicG9pbnRzdGl0bGVcIlxuICB9LCBbXCJcXG5cXHTllYblk4HmjqjojZBcXG5cIl0pLCBcIiBcIiwgX2goJ3VsJywge1xuICAgIGF0dHJzOiB7XG4gICAgICBcImlkXCI6IFwicHJvZHVjdFVsXCJcbiAgICB9XG4gIH0sIFtfdm0uX2woKF92bS5wcm9kdWN0cyksIGZ1bmN0aW9uKHApIHtcbiAgICByZXR1cm4gX2goJ2xpJywgW19oKCdyb3V0ZXItbGluaycsIHtcbiAgICAgIGF0dHJzOiB7XG4gICAgICAgIFwidG9cIjoge1xuICAgICAgICAgIG5hbWU6ICdwcm9kdWN0ZGV0YWlsJyxcbiAgICAgICAgICBwYXJhbXM6IHtcbiAgICAgICAgICAgIGlkOiBwLmlkXG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgfSwgW19oKCdpbWcnLCB7XG4gICAgICBhdHRyczoge1xuICAgICAgICBcInNyY1wiOiBwLnRodW1iXG4gICAgICB9XG4gICAgfSksIFwiIFwiLCBfaCgnaDMnLCBbX3ZtLl9zKHAubmFtZSldKSwgXCIgXCIsIF9oKCdwJywgW19oKCdzcGFuJywge1xuICAgICAgc3RhdGljQ2xhc3M6IFwicmVkXCJcbiAgICB9LCBbX3ZtLl9zKHAucG9pbnRzKV0pLCBcIuenr+WIhlwiXSldKV0pXG4gIH0pXSldKVxufSxzdGF0aWNSZW5kZXJGbnM6IFtmdW5jdGlvbiAoKXt2YXIgX3ZtPXRoaXM7dmFyIF9oPV92bS4kY3JlYXRlRWxlbWVudDtcbiAgcmV0dXJuIF9oKCdoZWFkZXInLCB7XG4gICAgYXR0cnM6IHtcbiAgICAgIFwiaWRcIjogXCJiaWdDdXN0b21lckhlYWRlclwiXG4gICAgfVxuICB9LCBbX2goJ2EnLCB7XG4gICAgc3RhdGljQ2xhc3M6IFwiYmFja1wiLFxuICAgIGF0dHJzOiB7XG4gICAgICBcImhyZWZcIjogXCJqYXZhc2NyaXB0OndpbmRvdy5oaXN0b3J5LmJhY2soKTtcIlxuICAgIH1cbiAgfSksIFwiXFxuXFx05oiR55qE56ev5YiGXFxuXCJdKVxufV19XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L3Z1ZS1sb2FkZXIvbGliL3RlbXBsYXRlLWNvbXBpbGVyLmpzP2lkPWRhdGEtdi0xNTk1YmJiMCEuL34vdnVlLWxvYWRlci9saWIvc2VsZWN0b3IuanM/dHlwZT10ZW1wbGF0ZSZpbmRleD0wIS4vc3JjL3ZpZXdzL215cG9pbnRzLnZ1ZVxuLy8gbW9kdWxlIGlkID0gOTdcbi8vIG1vZHVsZSBjaHVua3MgPSAzMyJdLCJzb3VyY2VSb290IjoiIn0=')}});