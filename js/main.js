"use strict";
window.util = {
    isEscEvent: function (t, e) {
        27 === t.keyCode && e()
    }, getScrollbarWidth: function () {
        return window.innerWidth - document.documentElement.clientWidth
    }
}, function () {
    var t = function (t) {
            var i = t.querySelector(".tabs__list").querySelectorAll(".tabs__item"),
                e = t.querySelectorAll(".tabs__content"), c = 0, n = !1, r = function () {
                    if (!n) {
                        var t = !1;
                        n = !0;
                        for (var e = 0; e < i.length; e++) {
                            var r = i[e];
                            t && r.classList.contains("tabs__item--active") && (t = !0, c = e), s(r, e)
                        }
                    }
                }, s = function (t, e) {
                    t.addEventListener("click", function (t) {
                        t.preventDefault(), a(e)
                    })
                }, a = function (t) {
                    if (t !== c) {
                        if (i[c].classList.remove("tabs__item--active"), i[t].classList.add("tabs__item--active"), e[c].classList.remove("tabs__content--active"), e[t].classList.add("tabs__content--active"), i[t].classList.contains("filters__button")) i[t].parentNode.parentNode.querySelector(".filters__button--active").classList.remove("filters__button--active"), i[t].classList.add("filters__button--active");
                        if (i[t].classList.contains("messages__contacts-tab")) i[t].parentNode.parentNode.querySelector(".messages__contacts-tab--active").classList.remove("messages__contacts-tab--active"), i[t].classList.add("messages__contacts-tab--active");
                        c = t
                    }
                };
            return r(), {init: r, goToTab: a}
        }, e = document.querySelector(".adding-post__tabs-wrapper"), r = document.querySelector(".profile__tabs-wrapper"),
        i = document.querySelector(".messages");
    if (e) t(e);
    if (r) t(r);
    if (i) t(i)
}(), document.querySelector(".modal--active"), document.querySelector(".modal"), document.querySelector(".modal--adding"), document.querySelector(".adding-post__submit"), window.util.getScrollbarWidth(), document.querySelector(".page__main-section"), document.querySelector(".footer__wrapper"), function () {
    var t = document.querySelector(".sorting");
    if (t) for (var e = t.querySelectorAll(".sorting__link"), r = t.querySelector(".sorting__link--active"), i = function (t) {
        t.preventDefault(), t.currentTarget === r ? r.classList.toggle("sorting__link--reverse") : (r.classList.remove("sorting__link--active"), t.currentTarget.classList.add("sorting__link--active"), r = t.currentTarget)
    }, c = 0; c < e.length; c++) e[c].addEventListener("click", i)
}(), function () {
    var t = document.querySelector(".filters");
    if (t) var e = t.querySelectorAll(".filters__button:not(.tabs__item)");
    if (e) for (var r = t.querySelector(".filters__button--active"), i = function (t) {
        t.preventDefault(), t.currentTarget !== r && (r.classList.remove("filters__button--active"), t.currentTarget.classList.add("filters__button--active"), r = t.currentTarget)
    }, c = 0; c < e.length; c++) e[c].addEventListener("click", i)
}();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1haW4uanMiXSwibmFtZXMiOlsid2luZG93IiwidXRpbCIsImlzRXNjRXZlbnQiLCJldnQiLCJjYiIsImtleUNvZGUiLCJnZXRTY3JvbGxiYXJXaWR0aCIsImlubmVyV2lkdGgiLCJkb2N1bWVudCIsImRvY3VtZW50RWxlbWVudCIsImNsaWVudFdpZHRoIiwic3dpdGNoVGFicyIsImJsb2NrIiwidGFiRWxlbWVudHMiLCJxdWVyeVNlbGVjdG9yIiwicXVlcnlTZWxlY3RvckFsbCIsInRhYkNvbnRlbnRTZWN0aW9ucyIsImFjdGl2ZVRhYkluZGV4IiwiaW5pdGlhbGl6ZWQiLCJpbml0aWFsaXplU3dpdGNoIiwiZGV0ZWN0ZWQiLCJpIiwibGVuZ3RoIiwidGFiIiwiY2xhc3NMaXN0IiwiY29udGFpbnMiLCJhZGRDbGlja0hhbmRsZSIsImluZGV4IiwiYWRkRXZlbnRMaXN0ZW5lciIsInByZXZlbnREZWZhdWx0IiwiZ29Ub1RhYiIsInJlbW92ZSIsImFkZCIsInBhcmVudE5vZGUiLCJpbml0IiwiYWRkaW5nUG9zdFRhYnMiLCJwcm9maWxlVGFicyIsIm1lc3NhZ2VzVGFicyIsInNvcnRpbmciLCJzb3J0aW5nTGlua3MiLCJzb3J0aW5nTGlua0FjdGl2ZSIsIm9uU29ydGluZ0l0ZW1DbGljayIsImN1cnJlbnRUYXJnZXQiLCJ0b2dnbGUiLCJmaWx0ZXJzIiwiZmlsdGVyc0J1dHRvbnMiLCJmaWx0ZXJzQnV0dG9uQWN0aXZlIiwib25GaWx0ZXJzQnV0dG9uQ2xpY2siXSwibWFwcGluZ3MiOiJBQUFBLGFBT0VBLE9BQU9DLEtBQU8sQ0FDWkMsV0FBWSxTQUFVQyxFQUFLQyxHQUhYLEtBSVZELEVBQUlFLFNBQ05ELEtBSUpFLGtCQUFtQixXQUNqQixPQUFPTixPQUFPTyxXQUFhQyxTQUFTQyxnQkFBZ0JDLGNBTzFELFdBQ0UsSUFBSUMsRUFBYSxTQUFVQyxHQUN6QixJQUNJQyxFQURXRCxFQUFNRSxjQUFjLGVBQ1JDLGlCQUFpQixlQUN4Q0MsRUFBcUJKLEVBQU1HLGlCQUFpQixrQkFDNUNFLEVBQWlCLEVBQ2pCQyxHQUFjLEVBRWRDLEVBQW1CLFdBQ3JCLElBQUtELEVBQWEsQ0FDaEIsSUFBSUUsR0FBVyxFQUNmRixHQUFjLEVBRWQsSUFBSyxJQUFJRyxFQUFJLEVBQUdBLEVBQUlSLEVBQVlTLE9BQVFELElBQUssQ0FDM0MsSUFBSUUsRUFBTVYsRUFBWVEsR0FDbEJELEdBQVlHLEVBQUlDLFVBQVVDLFNBQVMsd0JBQ3JDTCxHQUFXLEVBQ1hILEVBQWlCSSxHQUVuQkssRUFBZUgsRUFBS0YsTUFLdEJLLEVBQWlCLFNBQVVILEVBQUtJLEdBQ2xDSixFQUFJSyxpQkFBaUIsUUFBUyxTQUFVekIsR0FDdENBLEVBQUkwQixpQkFDSkMsRUFBUUgsTUFJUkcsRUFBVSxTQUFVSCxHQUN0QixHQUFJQSxJQUFVVixFQUFnQixDQUs1QixHQUpBSixFQUFZSSxHQUFnQk8sVUFBVU8sT0FBTyxzQkFDN0NsQixFQUFZYyxHQUFPSCxVQUFVUSxJQUFJLHNCQUNqQ2hCLEVBQW1CQyxHQUFnQk8sVUFBVU8sT0FBTyx5QkFDcERmLEVBQW1CVyxHQUFPSCxVQUFVUSxJQUFJLHlCQUNwQ25CLEVBQVljLEdBQU9ILFVBQVVDLFNBQVMsbUJBRXpCWixFQUFZYyxHQUFPTSxXQUFXQSxXQUFXbkIsY0FBYyw0QkFDekRVLFVBQVVPLE9BQU8sMkJBQ2ZsQixFQUFZYyxHQUNkSCxVQUFVUSxJQUFJLDJCQUU3QixHQUFJbkIsRUFBWWMsR0FBT0gsVUFBVUMsU0FBUywwQkFFeEJaLEVBQVljLEdBQU9NLFdBQVdBLFdBQVduQixjQUFjLG1DQUN6RFUsVUFBVU8sT0FBTyxrQ0FDZmxCLEVBQVljLEdBQ2RILFVBQVVRLElBQUksa0NBRTlCZixFQUFpQlUsSUFNckIsT0FGQVIsSUFFTyxDQUNMZSxLQUFNZixFQUNOVyxRQUFTQSxJQUlUSyxFQUFpQjNCLFNBQVNNLGNBQWMsOEJBQ3hDc0IsRUFBYzVCLFNBQVNNLGNBQWMsMEJBQ3JDdUIsRUFBZTdCLFNBQVNNLGNBQWMsYUFFMUMsR0FBSXFCLEVBQ3VCeEIsRUFBV3dCLEdBR3RDLEdBQUlDLEVBQ29CekIsRUFBV3lCLEdBR25DLEdBQUlDLEVBQ3FCMUIsRUFBVzBCLEdBNUV0QyxHQWtGb0I3QixTQUFTTSxjQUFjLGtCQUM3Qk4sU0FBU00sY0FBYyxVQUNqQk4sU0FBU00sY0FBYyxrQkFDbEJOLFNBQVNNLGNBQWMsd0JBQ3pCZCxPQUFPQyxLQUFLSyxvQkFDWEUsU0FBU00sY0FBYyx1QkFDekJOLFNBQVNNLGNBQWMsb0JBdUQ3QyxXQUNFLElBQUl3QixFQUFVOUIsU0FBU00sY0FBYyxZQUVyQyxHQUFJd0IsRUFtQkYsSUFsQkEsSUFBSUMsRUFBZUQsRUFBUXZCLGlCQUFpQixrQkFDeEN5QixFQUFvQkYsRUFBUXhCLGNBQWMsMEJBRTFDMkIsRUFBcUIsU0FBVXRDLEdBQ2pDQSxFQUFJMEIsaUJBQ0ExQixFQUFJdUMsZ0JBQWtCRixFQUN4QkEsRUFBa0JoQixVQUFVbUIsT0FBTywyQkFFbkNILEVBQWtCaEIsVUFBVU8sT0FBTyx5QkFDbkM1QixFQUFJdUMsY0FBY2xCLFVBQVVRLElBQUkseUJBQ2hDUSxFQUFvQnJDLEVBQUl1QyxnQkFRbkJyQixFQUFJLEVBQUdBLEVBQUlrQixFQUFhakIsT0FBUUQsSUFDcEJrQixFQUFhbEIsR0FKcEJPLGlCQUFpQixRQUFTYSxHQW5CNUMsR0E2QkEsV0FDRSxJQUFJRyxFQUFVcEMsU0FBU00sY0FBYyxZQUVyQyxHQUFJOEIsRUFDRixJQUFJQyxFQUFpQkQsRUFBUTdCLGlCQUFpQixxQ0FHaEQsR0FBSThCLEVBZ0JGLElBZkEsSUFBSUMsRUFBc0JGLEVBQVE5QixjQUFjLDRCQUU1Q2lDLEVBQXVCLFNBQVU1QyxHQUNuQ0EsRUFBSTBCLGlCQUNBMUIsRUFBSXVDLGdCQUFrQkksSUFDeEJBLEVBQW9CdEIsVUFBVU8sT0FBTywyQkFDckM1QixFQUFJdUMsY0FBY2xCLFVBQVVRLElBQUksMkJBQ2hDYyxFQUFzQjNDLEVBQUl1QyxnQkFRckJyQixFQUFJLEVBQUdBLEVBQUl3QixFQUFldkIsT0FBUUQsSUFDdEJ3QixFQUFleEIsR0FKcEJPLGlCQUFpQixRQUFTbUIsR0FwQjlDIiwiZmlsZSI6Im1haW4uanMiLCJzb3VyY2VzQ29udGVudCI6WyIndXNlIHN0cmljdCc7XHJcblxyXG4ndXNlIHNjcmlwdCc7XHJcblxyXG4oZnVuY3Rpb24gKCkge1xyXG4gIHZhciBFU0NfS0VZQ09ERSA9IDI3O1xyXG5cclxuICB3aW5kb3cudXRpbCA9IHtcclxuICAgIGlzRXNjRXZlbnQ6IGZ1bmN0aW9uIChldnQsIGNiKSB7XHJcbiAgICAgIGlmIChldnQua2V5Q29kZSA9PT0gRVNDX0tFWUNPREUpIHtcclxuICAgICAgICBjYigpO1xyXG4gICAgICB9XHJcbiAgICB9LFxyXG5cclxuICAgIGdldFNjcm9sbGJhcldpZHRoOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgIHJldHVybiB3aW5kb3cuaW5uZXJXaWR0aCAtIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5jbGllbnRXaWR0aDtcclxuICAgIH1cclxuICB9XHJcbn0pKCk7XHJcbid1c2Ugc2NyaXB0JztcclxuXHJcbi8v0YLQsNCx0YtcclxuKGZ1bmN0aW9uICgpIHtcclxuICB2YXIgc3dpdGNoVGFicyA9IGZ1bmN0aW9uIChibG9jaykge1xyXG4gICAgdmFyIHRhYnNMaXN0ID0gYmxvY2sucXVlcnlTZWxlY3RvcignLnRhYnNfX2xpc3QnKTtcclxuICAgIHZhciB0YWJFbGVtZW50cyA9IHRhYnNMaXN0LnF1ZXJ5U2VsZWN0b3JBbGwoJy50YWJzX19pdGVtJyk7XHJcbiAgICB2YXIgdGFiQ29udGVudFNlY3Rpb25zID0gYmxvY2sucXVlcnlTZWxlY3RvckFsbCgnLnRhYnNfX2NvbnRlbnQnKTtcclxuICAgIHZhciBhY3RpdmVUYWJJbmRleCA9IDA7XHJcbiAgICB2YXIgaW5pdGlhbGl6ZWQgPSBmYWxzZTtcclxuXHJcbiAgICB2YXIgaW5pdGlhbGl6ZVN3aXRjaCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgaWYgKCFpbml0aWFsaXplZCkge1xyXG4gICAgICAgIHZhciBkZXRlY3RlZCA9IGZhbHNlO1xyXG4gICAgICAgIGluaXRpYWxpemVkID0gdHJ1ZTtcclxuXHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCB0YWJFbGVtZW50cy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgdmFyIHRhYiA9IHRhYkVsZW1lbnRzW2ldO1xyXG4gICAgICAgICAgaWYgKGRldGVjdGVkICYmIHRhYi5jbGFzc0xpc3QuY29udGFpbnMoJ3RhYnNfX2l0ZW0tLWFjdGl2ZScpKSB7XHJcbiAgICAgICAgICAgIGRldGVjdGVkID0gdHJ1ZTtcclxuICAgICAgICAgICAgYWN0aXZlVGFiSW5kZXggPSBpO1xyXG4gICAgICAgICAgfVxyXG4gICAgICAgICAgYWRkQ2xpY2tIYW5kbGUodGFiLCBpKTtcclxuICAgICAgICB9XHJcbiAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgdmFyIGFkZENsaWNrSGFuZGxlID0gZnVuY3Rpb24gKHRhYiwgaW5kZXgpIHtcclxuICAgICAgdGFiLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGV2dCkge1xyXG4gICAgICAgIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgIGdvVG9UYWIoaW5kZXgpO1xyXG4gICAgICB9KTtcclxuICAgIH07XHJcblxyXG4gICAgdmFyIGdvVG9UYWIgPSBmdW5jdGlvbiAoaW5kZXgpIHtcclxuICAgICAgaWYgKGluZGV4ICE9PSBhY3RpdmVUYWJJbmRleCkge1xyXG4gICAgICAgIHRhYkVsZW1lbnRzW2FjdGl2ZVRhYkluZGV4XS5jbGFzc0xpc3QucmVtb3ZlKCd0YWJzX19pdGVtLS1hY3RpdmUnKTtcclxuICAgICAgICB0YWJFbGVtZW50c1tpbmRleF0uY2xhc3NMaXN0LmFkZCgndGFic19faXRlbS0tYWN0aXZlJyk7XHJcbiAgICAgICAgdGFiQ29udGVudFNlY3Rpb25zW2FjdGl2ZVRhYkluZGV4XS5jbGFzc0xpc3QucmVtb3ZlKCd0YWJzX19jb250ZW50LS1hY3RpdmUnKTtcclxuICAgICAgICB0YWJDb250ZW50U2VjdGlvbnNbaW5kZXhdLmNsYXNzTGlzdC5hZGQoJ3RhYnNfX2NvbnRlbnQtLWFjdGl2ZScpO1xyXG4gICAgICAgIGlmICh0YWJFbGVtZW50c1tpbmRleF0uY2xhc3NMaXN0LmNvbnRhaW5zKCdmaWx0ZXJzX19idXR0b24nKSkge1xyXG4gICAgICAgICAgdmFyIGFjdGl2ZUZpbHRlcjtcclxuICAgICAgICAgIGFjdGl2ZUZpbHRlciA9IHRhYkVsZW1lbnRzW2luZGV4XS5wYXJlbnROb2RlLnBhcmVudE5vZGUucXVlcnlTZWxlY3RvcignLmZpbHRlcnNfX2J1dHRvbi0tYWN0aXZlJyk7XHJcbiAgICAgICAgICBhY3RpdmVGaWx0ZXIuY2xhc3NMaXN0LnJlbW92ZSgnZmlsdGVyc19fYnV0dG9uLS1hY3RpdmUnKTtcclxuICAgICAgICAgIGFjdGl2ZUZpbHRlciA9IHRhYkVsZW1lbnRzW2luZGV4XTtcclxuICAgICAgICAgIGFjdGl2ZUZpbHRlci5jbGFzc0xpc3QuYWRkKCdmaWx0ZXJzX19idXR0b24tLWFjdGl2ZScpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAodGFiRWxlbWVudHNbaW5kZXhdLmNsYXNzTGlzdC5jb250YWlucygnbWVzc2FnZXNfX2NvbnRhY3RzLXRhYicpKSB7XHJcbiAgICAgICAgICB2YXIgYWN0aXZlQ29udGFjdDtcclxuICAgICAgICAgIGFjdGl2ZUNvbnRhY3QgPSB0YWJFbGVtZW50c1tpbmRleF0ucGFyZW50Tm9kZS5wYXJlbnROb2RlLnF1ZXJ5U2VsZWN0b3IoJy5tZXNzYWdlc19fY29udGFjdHMtdGFiLS1hY3RpdmUnKTtcclxuICAgICAgICAgIGFjdGl2ZUNvbnRhY3QuY2xhc3NMaXN0LnJlbW92ZSgnbWVzc2FnZXNfX2NvbnRhY3RzLXRhYi0tYWN0aXZlJyk7XHJcbiAgICAgICAgICBhY3RpdmVDb250YWN0ID0gdGFiRWxlbWVudHNbaW5kZXhdO1xyXG4gICAgICAgICAgYWN0aXZlQ29udGFjdC5jbGFzc0xpc3QuYWRkKCdtZXNzYWdlc19fY29udGFjdHMtdGFiLS1hY3RpdmUnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgYWN0aXZlVGFiSW5kZXggPSBpbmRleDtcclxuICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBpbml0aWFsaXplU3dpdGNoKCk7XHJcblxyXG4gICAgcmV0dXJuIHtcclxuICAgICAgaW5pdDogaW5pdGlhbGl6ZVN3aXRjaCxcclxuICAgICAgZ29Ub1RhYjogZ29Ub1RhYlxyXG4gICAgfTtcclxuICB9XHJcblxyXG4gIHZhciBhZGRpbmdQb3N0VGFicyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJy5hZGRpbmctcG9zdF9fdGFicy13cmFwcGVyJyk7XHJcbiAgdmFyIHByb2ZpbGVUYWJzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLnByb2ZpbGVfX3RhYnMtd3JhcHBlcicpO1xyXG4gIHZhciBtZXNzYWdlc1RhYnMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcubWVzc2FnZXMnKTtcclxuXHJcbiAgaWYgKGFkZGluZ1Bvc3RUYWJzKSB7XHJcbiAgICB2YXIgYWRkaW5nUG9zdENvbGxiYWNrID0gc3dpdGNoVGFicyhhZGRpbmdQb3N0VGFicyk7XHJcbiAgfVxyXG5cclxuICBpZiAocHJvZmlsZVRhYnMpIHtcclxuICAgIHZhciBwcm9maWxlQ29sbGJhY2sgPSBzd2l0Y2hUYWJzKHByb2ZpbGVUYWJzKTtcclxuICB9XHJcblxyXG4gIGlmIChtZXNzYWdlc1RhYnMpIHtcclxuICAgIHZhciBtZXNzYWdlc0NvbGxiYWNrID0gc3dpdGNoVGFicyhtZXNzYWdlc1RhYnMpO1xyXG4gIH1cclxufSkoKTtcclxuJ3VzZSBzY3JpcHQnO1xyXG5cclxuKGZ1bmN0aW9uICgpIHtcclxuICB2YXIgYWN0aXZlTW9kYWwgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcubW9kYWwtLWFjdGl2ZScpO1xyXG4gIHZhciBtb2RhbCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJy5tb2RhbCcpO1xyXG4gIHZhciBtb2RhbEFkZGluZyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJy5tb2RhbC0tYWRkaW5nJyk7XHJcbiAgdmFyIGFkZGluZ1Bvc3RTdWJtaXQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuYWRkaW5nLXBvc3RfX3N1Ym1pdCcpO1xyXG4gIHZhciBzY3JvbGxiYXJXaWR0aCA9IHdpbmRvdy51dGlsLmdldFNjcm9sbGJhcldpZHRoKCkgKyAncHgnO1xyXG4gIHZhciBwYWdlTWFpblNlY3Rpb24gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcucGFnZV9fbWFpbi1zZWN0aW9uJyk7XHJcbiAgdmFyIGZvb3RlcldyYXBwZXIgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuZm9vdGVyX193cmFwcGVyJyk7XHJcblxyXG4gIHZhciBzaG93TW9kYWwgPSBmdW5jdGlvbiAob3BlbkJ1dHRvbiwgbW9kYWwpIHtcclxuICAgIHZhciBjbG9zZUJ1dHRvbiA9IG1vZGFsLnF1ZXJ5U2VsZWN0b3IoJy5tb2RhbF9fY2xvc2UtYnV0dG9uJyk7XHJcblxyXG4gICAgdmFyIG9uUG9wdXBFc2NQcmVzcyA9IGZ1bmN0aW9uIChldnQpIHtcclxuICAgICAgd2luZG93LnV0aWwuaXNFc2NFdmVudChldnQsIGNsb3NlTW9kYWwpO1xyXG4gICAgfTtcclxuXHJcbiAgICB2YXIgY2xvc2VNb2RhbCA9IGZ1bmN0aW9uIChldnQpIHtcclxuICAgICAgbW9kYWwuY2xhc3NMaXN0LnJlbW92ZSgnbW9kYWwtLWFjdGl2ZScpO1xyXG4gICAgICBhY3RpdmVNb2RhbCA9IGZhbHNlO1xyXG4gICAgICBkb2N1bWVudC5yZW1vdmVFdmVudExpc3RlbmVyKCdrZXlkb3duJywgb25Qb3B1cEVzY1ByZXNzKTtcclxuICAgICAgZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LnN0eWxlLm92ZXJmbG93WSA9ICdhdXRvJztcclxuICAgICAgcGFnZU1haW5TZWN0aW9uLnN0eWxlLnBhZGRpbmdSaWdodCA9ICcwJztcclxuICAgICAgZm9vdGVyV3JhcHBlci5zdHlsZS5wYWRkaW5nUmlnaHQgPSAnMCc7XHJcbiAgICB9XHJcblxyXG4gICAgdmFyIG9wZW5Nb2RhbCA9IGZ1bmN0aW9uIChldnQpIHtcclxuICAgICAgaWYgKGFjdGl2ZU1vZGFsKSB7XHJcbiAgICAgICAgYWN0aXZlTW9kYWwuY2xhc3NMaXN0LnJlbW92ZSgnbW9kYWwtLWFjdGl2ZScpO1xyXG4gICAgICB9XHJcblxyXG4gICAgICBtb2RhbC5jbGFzc0xpc3QuYWRkKCdtb2RhbC0tYWN0aXZlJyk7XHJcbiAgICAgIGFjdGl2ZU1vZGFsID0gbW9kYWw7XHJcbiAgICAgIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5zdHlsZS5vdmVyZmxvd1kgPSAnaGlkZGVuJztcclxuICAgICAgcGFnZU1haW5TZWN0aW9uLnN0eWxlLnBhZGRpbmdSaWdodCA9IHNjcm9sbGJhcldpZHRoO1xyXG4gICAgICBmb290ZXJXcmFwcGVyLnN0eWxlLnBhZGRpbmdSaWdodCA9IHNjcm9sbGJhcldpZHRoO1xyXG4gICAgICBjbG9zZUJ1dHRvbi5mb2N1cygpO1xyXG5cclxuICAgICAgY2xvc2VCdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbiAoZXZ0KSB7XHJcbiAgICAgICAgZXZ0LnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgY2xvc2VNb2RhbCgpO1xyXG4gICAgICB9KTtcclxuXHJcbiAgICAgIG1vZGFsLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGV2dCkge1xyXG4gICAgICAgIGlmIChldnQudGFyZ2V0ID09PSBtb2RhbCkge1xyXG4gICAgICAgICAgY2xvc2VNb2RhbCgpO1xyXG4gICAgICAgIH1cclxuICAgICAgfSlcclxuXHJcbiAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2tleWRvd24nLCBvblBvcHVwRXNjUHJlc3MpO1xyXG4gICAgfVxyXG5cclxuICAgIG9wZW5CdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbiAoZXZ0KSB7XHJcbiAgICAgIG9wZW5Nb2RhbCgpO1xyXG4gICAgfSk7XHJcbiAgfVxyXG5cclxuICAvLyBpZiAobW9kYWwpIHtcclxuICAvLyAgIHNob3dNb2RhbChhZGRpbmdQb3N0U3VibWl0LCBtb2RhbEFkZGluZyk7XHJcbiAgLy8gfVxyXG59KSgpO1xyXG4ndXNlIHNjcmlwdCc7XHJcblxyXG4oZnVuY3Rpb24gKCkge1xyXG4gIHZhciBzb3J0aW5nID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLnNvcnRpbmcnKTtcclxuXHJcbiAgaWYgKHNvcnRpbmcpIHtcclxuICAgIHZhciBzb3J0aW5nTGlua3MgPSBzb3J0aW5nLnF1ZXJ5U2VsZWN0b3JBbGwoJy5zb3J0aW5nX19saW5rJyk7XHJcbiAgICB2YXIgc29ydGluZ0xpbmtBY3RpdmUgPSBzb3J0aW5nLnF1ZXJ5U2VsZWN0b3IoJy5zb3J0aW5nX19saW5rLS1hY3RpdmUnKTtcclxuXHJcbiAgICB2YXIgb25Tb3J0aW5nSXRlbUNsaWNrID0gZnVuY3Rpb24gKGV2dCkge1xyXG4gICAgICBldnQucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgaWYgKGV2dC5jdXJyZW50VGFyZ2V0ID09PSBzb3J0aW5nTGlua0FjdGl2ZSkge1xyXG4gICAgICAgIHNvcnRpbmdMaW5rQWN0aXZlLmNsYXNzTGlzdC50b2dnbGUoJ3NvcnRpbmdfX2xpbmstLXJldmVyc2UnKTtcclxuICAgICAgfSBlbHNlIHtcclxuICAgICAgICBzb3J0aW5nTGlua0FjdGl2ZS5jbGFzc0xpc3QucmVtb3ZlKCdzb3J0aW5nX19saW5rLS1hY3RpdmUnKTtcclxuICAgICAgICBldnQuY3VycmVudFRhcmdldC5jbGFzc0xpc3QuYWRkKCdzb3J0aW5nX19saW5rLS1hY3RpdmUnKTtcclxuICAgICAgICBzb3J0aW5nTGlua0FjdGl2ZSA9IGV2dC5jdXJyZW50VGFyZ2V0O1xyXG4gICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgdmFyIGFkZFNvcnRpbmdMaXN0ZW5lciA9IGZ1bmN0aW9uIChzb3J0aW5nSXRlbSkge1xyXG4gICAgICBzb3J0aW5nSXRlbS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIG9uU29ydGluZ0l0ZW1DbGljayk7XHJcbiAgICB9XHJcblxyXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBzb3J0aW5nTGlua3MubGVuZ3RoOyBpKyspIHtcclxuICAgICAgYWRkU29ydGluZ0xpc3RlbmVyKHNvcnRpbmdMaW5rc1tpXSk7XHJcbiAgICB9XHJcbiAgfVxyXG59KSgpO1xyXG4ndXNlIHNjcmlwdCc7XHJcblxyXG4oZnVuY3Rpb24gKCkge1xyXG4gIHZhciBmaWx0ZXJzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLmZpbHRlcnMnKTtcclxuXHJcbiAgaWYgKGZpbHRlcnMpIHtcclxuICAgIHZhciBmaWx0ZXJzQnV0dG9ucyA9IGZpbHRlcnMucXVlcnlTZWxlY3RvckFsbCgnLmZpbHRlcnNfX2J1dHRvbjpub3QoLnRhYnNfX2l0ZW0pJyk7XHJcbiAgfVxyXG5cclxuICBpZiAoZmlsdGVyc0J1dHRvbnMpIHtcclxuICAgIHZhciBmaWx0ZXJzQnV0dG9uQWN0aXZlID0gZmlsdGVycy5xdWVyeVNlbGVjdG9yKCcuZmlsdGVyc19fYnV0dG9uLS1hY3RpdmUnKTtcclxuXHJcbiAgICB2YXIgb25GaWx0ZXJzQnV0dG9uQ2xpY2sgPSBmdW5jdGlvbiAoZXZ0KSB7XHJcbiAgICAgIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICBpZiAoZXZ0LmN1cnJlbnRUYXJnZXQgIT09IGZpbHRlcnNCdXR0b25BY3RpdmUpIHtcclxuICAgICAgICBmaWx0ZXJzQnV0dG9uQWN0aXZlLmNsYXNzTGlzdC5yZW1vdmUoJ2ZpbHRlcnNfX2J1dHRvbi0tYWN0aXZlJyk7XHJcbiAgICAgICAgZXZ0LmN1cnJlbnRUYXJnZXQuY2xhc3NMaXN0LmFkZCgnZmlsdGVyc19fYnV0dG9uLS1hY3RpdmUnKTtcclxuICAgICAgICBmaWx0ZXJzQnV0dG9uQWN0aXZlID0gZXZ0LmN1cnJlbnRUYXJnZXQ7XHJcbiAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICB2YXIgYWRkRmlsdGVyc0xpc3RlbmVyID0gZnVuY3Rpb24gKGZpbHRlcnNCdXR0b24pIHtcclxuICAgICAgZmlsdGVyc0J1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIG9uRmlsdGVyc0J1dHRvbkNsaWNrKTtcclxuICAgIH1cclxuXHJcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGZpbHRlcnNCdXR0b25zLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgIGFkZEZpbHRlcnNMaXN0ZW5lcihmaWx0ZXJzQnV0dG9uc1tpXSk7XHJcbiAgICB9XHJcbiAgfVxyXG59KSgpOyJdfQ==
