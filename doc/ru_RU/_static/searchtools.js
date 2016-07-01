/*
 * searchtools.js_t
 * ~~~~~~~~~~~~~~~~
 *
 * Sphinx JavaScript utilties for the full-text search.
 *
 * :copyright: Copyright 2007-2015 by the Sphinx team, see AUTHORS.
 * :license: BSD, see LICENSE for details.
 *
 */


/* Non-minified version JS is _stemmer.js if file is provided */ 
var JSX={};(function(h){function j(b,e){var a=function(){};a.prototype=e.prototype;var c=new a;for(var d in b){b[d].prototype=c}}function J(c,b){for(var a in b.prototype)if(b.prototype.hasOwnProperty(a))c.prototype[a]=b.prototype[a]}function f(a,b,d){function c(a,b,c){delete a[b];a[b]=c;return c}Object.defineProperty(a,b,{get:function(){return c(a,b,d())},set:function(d){c(a,b,d)},enumerable:true,configurable:true})}function K(a,b,c){return a[b]=a[b]/c|0}var p=parseInt;var z=parseFloat;function L(a){return a!==a}var x=isFinite;var w=encodeURIComponent;var u=decodeURIComponent;var t=encodeURI;var s=decodeURI;var B=Object.prototype.toString;var q=Object.prototype.hasOwnProperty;function i(){}h.require=function(b){var a=o[b];return a!==undefined?a:null};h.profilerIsRunning=function(){return i.getResults!=null};h.getProfileResults=function(){return(i.getResults||function(){return{}})()};h.postProfileResults=function(a,b){if(i.postResults==null)throw new Error('profiler has not been turned on');return i.postResults(a,b)};h.resetProfileResults=function(){if(i.resetResults==null)throw new Error('profiler has not been turned on');return i.resetResults()};h.DEBUG=false;function r(){};j([r],Error);function a(a,b,c){this.G=a.length;this.X=a;this.a=b;this.J=c;this.I=null;this.b=null};j([a],Object);function m(){};j([m],Object);function g(){var a;var b;var c;this.F={};a=this.D='';b=this._=0;c=this.A=a.length;this.E=0;this.B=b;this.C=c};j([g],m);function v(a,b){a.D=b.D;a._=b._;a.A=b.A;a.E=b.E;a.B=b.B;a.C=b.C};function k(b,d,c,e){var a;if(b._>=b.A){return false}a=b.D.charCodeAt(b._);if(a>e||a<c){return false}a-=c;if((d[a>>>3]&1<<(a&7))===0){return false}b._++;return true};function l(a,d,c,e){var b;if(a._>=a.A){return false}b=a.D.charCodeAt(a._);if(b>e||b<c){a._++;return true}b-=c;if((d[b>>>3]&1<<(b&7))===0){a._++;return true}return false};function d(a,b,d){var c;if(a._-a.E<b){return false}if(a.D.slice((c=a._)-b,c)!==d){return false}a._-=b;return true};function e(d,m,p){var b;var g;var e;var n;var f;var k;var l;var i;var h;var c;var a;var j;var o;b=0;g=p;e=d._;n=d.E;f=0;k=0;l=false;while(true){i=b+(g-b>>1);h=0;c=f<k?f:k;a=m[i];for(j=a.G-1-c;j>=0;j--){if(e-c===n){h=-1;break}h=d.D.charCodeAt(e-1-c)-a.X.charCodeAt(j);if(h!==0){break}c++}if(h<0){g=i;k=c}else{b=i;f=c}if(g-b<=1){if(b>0){break}if(g===b){break}if(l){break}l=true}}while(true){a=m[b];if(f>=a.G){d._=e-a.G|0;if(a.I==null){return a.J}o=a.I(d);d._=e-a.G|0;if(o){return a.J}}b=a.a;if(b<0){return 0}}return-1};function A(a,b,d,e){var c;c=e.length-(d-b);a.D=a.D.slice(0,b)+e+a.D.slice(d);a.A+=c|0;if(a._>=d){a._+=c|0}else if(a._>b){a._=b}return c|0};function c(a,f){var b;var c;var d;var e;b=false;if((c=a.B)<0||c>(d=a.C)||d>(e=a.A)||e>a.D.length?false:true){A(a,a.B,a.C,f);b=true}return b};g.prototype.H=function(){return false};g.prototype.Y=function(b){var a;var c;var d;var e;a=this.F['.'+b];if(a==null){c=this.D=b;d=this._=0;e=this.A=c.length;this.E=0;this.B=d;this.C=e;this.H();a=this.D;this.F['.'+b]=a}return a};g.prototype.stemWord=g.prototype.Y;g.prototype.Z=function(e){var d;var b;var c;var a;var f;var g;var h;d=[];for(b=0;b<e.length;b++){c=e[b];a=this.F['.'+c];if(a==null){f=this.D=c;g=this._=0;h=this.A=f.length;this.E=0;this.B=g;this.C=h;this.H();a=this.D;this.F['.'+c]=a}d.push(a)}return d};g.prototype.stemWords=g.prototype.Z;function b(){g.call(this);this.I_p2=0;this.I_pV=0};j([b],g);b.prototype.K=function(a){this.I_p2=a.I_p2;this.I_pV=a.I_pV;v(this,a)};b.prototype.copy_from=b.prototype.K;b.prototype.R=function(){var g;var a;var c;var d;var e;var f;var h;this.I_pV=h=this.A;this.I_p2=h;g=this._;a=true;a:while(a===true){a=false;b:while(true){c=true;c:while(c===true){c=false;if(!k(this,b.g_v,1072,1103)){break c}break b}if(this._>=this.A){break a}this._++}this.I_pV=this._;b:while(true){d=true;c:while(d===true){d=false;if(!l(this,b.g_v,1072,1103)){break c}break b}if(this._>=this.A){break a}this._++}b:while(true){e=true;c:while(e===true){e=false;if(!k(this,b.g_v,1072,1103)){break c}break b}if(this._>=this.A){break a}this._++}b:while(true){f=true;c:while(f===true){f=false;if(!l(this,b.g_v,1072,1103)){break c}break b}if(this._>=this.A){break a}this._++}this.I_p2=this._}this._=g;return true};b.prototype.r_mark_regions=b.prototype.R;function D(a){var h;var c;var d;var e;var f;var g;var i;a.I_pV=i=a.A;a.I_p2=i;h=a._;c=true;a:while(c===true){c=false;b:while(true){d=true;c:while(d===true){d=false;if(!k(a,b.g_v,1072,1103)){break c}break b}if(a._>=a.A){break a}a._++}a.I_pV=a._;b:while(true){e=true;c:while(e===true){e=false;if(!l(a,b.g_v,1072,1103)){break c}break b}if(a._>=a.A){break a}a._++}b:while(true){f=true;c:while(f===true){f=false;if(!k(a,b.g_v,1072,1103)){break c}break b}if(a._>=a.A){break a}a._++}b:while(true){g=true;c:while(g===true){g=false;if(!l(a,b.g_v,1072,1103)){break c}break b}if(a._>=a.A){break a}a._++}a.I_p2=a._}a._=h;return true};b.prototype.N=function(){return!(this.I_p2<=this._)?false:true};b.prototype.r_R2=b.prototype.N;b.prototype.T=function(){var a;var h;var f;var g;this.C=this._;a=e(this,b.a_0,9);if(a===0){return false}this.B=this._;switch(a){case 0:return false;case 1:f=true;a:while(f===true){f=false;h=this.A-this._;g=true;b:while(g===true){g=false;if(!d(this,1,'а')){break b}break a}this._=this.A-h;if(!d(this,1,'я')){return false}}if(!c(this,'')){return false}break;case 2:if(!c(this,'')){return false}break}return true};b.prototype.r_perfective_gerund=b.prototype.T;function E(a){var f;var i;var g;var h;a.C=a._;f=e(a,b.a_0,9);if(f===0){return false}a.B=a._;switch(f){case 0:return false;case 1:g=true;a:while(g===true){g=false;i=a.A-a._;h=true;b:while(h===true){h=false;if(!d(a,1,'а')){break b}break a}a._=a.A-i;if(!d(a,1,'я')){return false}}if(!c(a,'')){return false}break;case 2:if(!c(a,'')){return false}break}return true};b.prototype.P=function(){var a;this.C=this._;a=e(this,b.a_1,26);if(a===0){return false}this.B=this._;switch(a){case 0:return false;case 1:if(!c(this,'')){return false}break}return true};b.prototype.r_adjective=b.prototype.P;function n(a){var d;a.C=a._;d=e(a,b.a_1,26);if(d===0){return false}a.B=a._;switch(d){case 0:return false;case 1:if(!c(a,'')){return false}break}return true};b.prototype.O=function(){var f;var a;var j;var g;var h;var i;if(!n(this)){return false}a=this.A-this._;g=true;a:while(g===true){g=false;this.C=this._;f=e(this,b.a_2,8);if(f===0){this._=this.A-a;break a}this.B=this._;switch(f){case 0:this._=this.A-a;break a;case 1:h=true;b:while(h===true){h=false;j=this.A-this._;i=true;c:while(i===true){i=false;if(!d(this,1,'а')){break c}break b}this._=this.A-j;if(!d(this,1,'я')){this._=this.A-a;break a}}if(!c(this,'')){return false}break;case 2:if(!c(this,'')){return false}break}}return true};b.prototype.r_adjectival=b.prototype.O;function G(a){var g;var f;var k;var h;var i;var j;if(!n(a)){return false}f=a.A-a._;h=true;a:while(h===true){h=false;a.C=a._;g=e(a,b.a_2,8);if(g===0){a._=a.A-f;break a}a.B=a._;switch(g){case 0:a._=a.A-f;break a;case 1:i=true;b:while(i===true){i=false;k=a.A-a._;j=true;c:while(j===true){j=false;if(!d(a,1,'а')){break c}break b}a._=a.A-k;if(!d(a,1,'я')){a._=a.A-f;break a}}if(!c(a,'')){return false}break;case 2:if(!c(a,'')){return false}break}}return true};b.prototype.U=function(){var a;this.C=this._;a=e(this,b.a_3,2);if(a===0){return false}this.B=this._;switch(a){case 0:return false;case 1:if(!c(this,'')){return false}break}return true};b.prototype.r_reflexive=b.prototype.U;function H(a){var d;a.C=a._;d=e(a,b.a_3,2);if(d===0){return false}a.B=a._;switch(d){case 0:return false;case 1:if(!c(a,'')){return false}break}return true};b.prototype.W=function(){var a;var h;var f;var g;this.C=this._;a=e(this,b.a_4,46);if(a===0){return false}this.B=this._;switch(a){case 0:return false;case 1:f=true;a:while(f===true){f=false;h=this.A-this._;g=true;b:while(g===true){g=false;if(!d(this,1,'а')){break b}break a}this._=this.A-h;if(!d(this,1,'я')){return false}}if(!c(this,'')){return false}break;case 2:if(!c(this,'')){return false}break}return true};b.prototype.r_verb=b.prototype.W;function I(a){var f;var i;var g;var h;a.C=a._;f=e(a,b.a_4,46);if(f===0){return false}a.B=a._;switch(f){case 0:return false;case 1:g=true;a:while(g===true){g=false;i=a.A-a._;h=true;b:while(h===true){h=false;if(!d(a,1,'а')){break b}break a}a._=a.A-i;if(!d(a,1,'я')){return false}}if(!c(a,'')){return false}break;case 2:if(!c(a,'')){return false}break}return true};b.prototype.S=function(){var a;this.C=this._;a=e(this,b.a_5,36);if(a===0){return false}this.B=this._;switch(a){case 0:return false;case 1:if(!c(this,'')){return false}break}return true};b.prototype.r_noun=b.prototype.S;function F(a){var d;a.C=a._;d=e(a,b.a_5,36);if(d===0){return false}a.B=a._;switch(d){case 0:return false;case 1:if(!c(a,'')){return false}break}return true};b.prototype.Q=function(){var a;var d;this.C=this._;a=e(this,b.a_6,2);if(a===0){return false}this.B=d=this._;if(!(!(this.I_p2<=d)?false:true)){return false}switch(a){case 0:return false;case 1:if(!c(this,'')){return false}break}return true};b.prototype.r_derivational=b.prototype.Q;function C(a){var d;var f;a.C=a._;d=e(a,b.a_6,2);if(d===0){return false}a.B=f=a._;if(!(!(a.I_p2<=f)?false:true)){return false}switch(d){case 0:return false;case 1:if(!c(a,'')){return false}break}return true};b.prototype.V=function(){var a;this.C=this._;a=e(this,b.a_7,4);if(a===0){return false}this.B=this._;switch(a){case 0:return false;case 1:if(!c(this,'')){return false}this.C=this._;if(!d(this,1,'н')){return false}this.B=this._;if(!d(this,1,'н')){return false}if(!c(this,'')){return false}break;case 2:if(!d(this,1,'н')){return false}if(!c(this,'')){return false}break;case 3:if(!c(this,'')){return false}break}return true};b.prototype.r_tidy_up=b.prototype.V;function y(a){var f;a.C=a._;f=e(a,b.a_7,4);if(f===0){return false}a.B=a._;switch(f){case 0:return false;case 1:if(!c(a,'')){return false}a.C=a._;if(!d(a,1,'н')){return false}a.B=a._;if(!d(a,1,'н')){return false}if(!c(a,'')){return false}break;case 2:if(!d(a,1,'н')){return false}if(!c(a,'')){return false}break;case 3:if(!c(a,'')){return false}break}return true};b.prototype.H=function(){var s;var v;var w;var A;var p;var q;var i;var t;var u;var e;var f;var g;var h;var a;var j;var b;var k;var l;var m;var n;var x;var z;var o;var B;var J;var K;var L;var M;var N;var O;var r;s=this._;e=true;a:while(e===true){e=false;if(!D(this)){break a}}x=this._=s;this.E=x;o=this._=z=this.A;v=z-o;if(o<this.I_pV){return false}K=this._=this.I_pV;w=this.E;this.E=K;M=this._=(L=this.A)-v;A=L-M;f=true;c:while(f===true){f=false;g=true;b:while(g===true){g=false;p=this.A-this._;h=true;a:while(h===true){h=false;if(!E(this)){break a}break b}J=this._=(B=this.A)-p;q=B-J;a=true;a:while(a===true){a=false;if(!H(this)){this._=this.A-q;break a}}j=true;a:while(j===true){j=false;i=this.A-this._;b=true;d:while(b===true){b=false;if(!G(this)){break d}break a}this._=this.A-i;k=true;d:while(k===true){k=false;if(!I(this)){break d}break a}this._=this.A-i;if(!F(this)){break c}}}}O=this._=(N=this.A)-A;t=N-O;l=true;a:while(l===true){l=false;this.C=this._;if(!d(this,1,'и')){this._=this.A-t;break a}this.B=this._;if(!c(this,'')){return false}}u=this.A-this._;m=true;a:while(m===true){m=false;if(!C(this)){break a}}this._=this.A-u;n=true;a:while(n===true){n=false;if(!y(this)){break a}}r=this.E=w;this._=r;return true};b.prototype.stem=b.prototype.H;b.prototype.L=function(a){return a instanceof b};b.prototype.equals=b.prototype.L;b.prototype.M=function(){var c;var a;var b;var d;c='RussianStemmer';a=0;for(b=0;b<c.length;b++){d=c.charCodeAt(b);a=(a<<5)-a+d;a=a&a}return a|0};b.prototype.hashCode=b.prototype.M;b.serialVersionUID=1;f(b,'methodObject',function(){return new b});f(b,'a_0',function(){return[new a('в',-1,1),new a('ив',0,2),new a('ыв',0,2),new a('вши',-1,1),new a('ивши',3,2),new a('ывши',3,2),new a('вшись',-1,1),new a('ившись',6,2),new a('ывшись',6,2)]});f(b,'a_1',function(){return[new a('ее',-1,1),new a('ие',-1,1),new a('ое',-1,1),new a('ые',-1,1),new a('ими',-1,1),new a('ыми',-1,1),new a('ей',-1,1),new a('ий',-1,1),new a('ой',-1,1),new a('ый',-1,1),new a('ем',-1,1),new a('им',-1,1),new a('ом',-1,1),new a('ым',-1,1),new a('его',-1,1),new a('ого',-1,1),new a('ему',-1,1),new a('ому',-1,1),new a('их',-1,1),new a('ых',-1,1),new a('ею',-1,1),new a('ою',-1,1),new a('ую',-1,1),new a('юю',-1,1),new a('ая',-1,1),new a('яя',-1,1)]});f(b,'a_2',function(){return[new a('ем',-1,1),new a('нн',-1,1),new a('вш',-1,1),new a('ивш',2,2),new a('ывш',2,2),new a('щ',-1,1),new a('ющ',5,1),new a('ующ',6,2)]});f(b,'a_3',function(){return[new a('сь',-1,1),new a('ся',-1,1)]});f(b,'a_4',function(){return[new a('ла',-1,1),new a('ила',0,2),new a('ыла',0,2),new a('на',-1,1),new a('ена',3,2),new a('ете',-1,1),new a('ите',-1,2),new a('йте',-1,1),new a('ейте',7,2),new a('уйте',7,2),new a('ли',-1,1),new a('или',10,2),new a('ыли',10,2),new a('й',-1,1),new a('ей',13,2),new a('уй',13,2),new a('л',-1,1),new a('ил',16,2),new a('ыл',16,2),new a('ем',-1,1),new a('им',-1,2),new a('ым',-1,2),new a('н',-1,1),new a('ен',22,2),new a('ло',-1,1),new a('ило',24,2),new a('ыло',24,2),new a('но',-1,1),new a('ено',27,2),new a('нно',27,1),new a('ет',-1,1),new a('ует',30,2),new a('ит',-1,2),new a('ыт',-1,2),new a('ют',-1,1),new a('уют',34,2),new a('ят',-1,2),new a('ны',-1,1),new a('ены',37,2),new a('ть',-1,1),new a('ить',39,2),new a('ыть',39,2),new a('ешь',-1,1),new a('ишь',-1,2),new a('ю',-1,2),new a('ую',44,2)]});f(b,'a_5',function(){return[new a('а',-1,1),new a('ев',-1,1),new a('ов',-1,1),new a('е',-1,1),new a('ие',3,1),new a('ье',3,1),new a('и',-1,1),new a('еи',6,1),new a('ии',6,1),new a('ами',6,1),new a('ями',6,1),new a('иями',10,1),new a('й',-1,1),new a('ей',12,1),new a('ией',13,1),new a('ий',12,1),new a('ой',12,1),new a('ам',-1,1),new a('ем',-1,1),new a('ием',18,1),new a('ом',-1,1),new a('ям',-1,1),new a('иям',21,1),new a('о',-1,1),new a('у',-1,1),new a('ах',-1,1),new a('ях',-1,1),new a('иях',26,1),new a('ы',-1,1),new a('ь',-1,1),new a('ю',-1,1),new a('ию',30,1),new a('ью',30,1),new a('я',-1,1),new a('ия',33,1),new a('ья',33,1)]});f(b,'a_6',function(){return[new a('ост',-1,1),new a('ость',-1,1)]});f(b,'a_7',function(){return[new a('ейше',-1,1),new a('н',-1,2),new a('ейш',-1,1),new a('ь',-1,3)]});f(b,'g_v',function(){return[33,65,8,232]});var o={'src/stemmer.jsx':{Stemmer:m},'src/russian-stemmer.jsx':{RussianStemmer:b}}}(JSX))
var Stemmer = JSX.require("src/russian-stemmer.jsx").RussianStemmer;



/**
 * Simple result scoring code.
 */
var Scorer = {
  // Implement the following function to further tweak the score for each result
  // The function takes a result array [filename, title, anchor, descr, score]
  // and returns the new score.
  /*
  score: function(result) {
    return result[4];
  },
  */

  // query matches the full name of an object
  objNameMatch: 11,
  // or matches in the last dotted part of the object name
  objPartialMatch: 6,
  // Additive scores depending on the priority of the object
  objPrio: {0:  15,   // used to be importantResults
            1:  5,   // used to be objectResults
            2: -5},  // used to be unimportantResults
  //  Used when the priority is not in the mapping.
  objPrioDefault: 0,

  // query found in title
  title: 15,
  // query found in terms
  term: 5
};


/**
 * Search Module
 */
var Search = {

  _index : null,
  _queued_query : null,
  _pulse_status : -1,

  init : function() {
      var params = $.getQueryParameters();
      if (params.q) {
          var query = params.q[0];
          $('input[name="q"]')[0].value = query;
          this.performSearch(query);
      }
  },

  loadIndex : function(url) {
    $.ajax({type: "GET", url: url, data: null,
            dataType: "script", cache: true,
            complete: function(jqxhr, textstatus) {
              if (textstatus != "success") {
                document.getElementById("searchindexloader").src = url;
              }
            }});
  },

  setIndex : function(index) {
    var q;
    this._index = index;
    if ((q = this._queued_query) !== null) {
      this._queued_query = null;
      Search.query(q);
    }
  },

  hasIndex : function() {
      return this._index !== null;
  },

  deferQuery : function(query) {
      this._queued_query = query;
  },

  stopPulse : function() {
      this._pulse_status = 0;
  },

  startPulse : function() {
    if (this._pulse_status >= 0)
        return;
    function pulse() {
      var i;
      Search._pulse_status = (Search._pulse_status + 1) % 4;
      var dotString = '';
      for (i = 0; i < Search._pulse_status; i++)
        dotString += '.';
      Search.dots.text(dotString);
      if (Search._pulse_status > -1)
        window.setTimeout(pulse, 500);
    }
    pulse();
  },

  /**
   * perform a search for something (or wait until index is loaded)
   */
  performSearch : function(query) {
    // create the required interface elements
    this.out = $('#search-results');
    this.title = $('<h2>' + _('Searching') + '</h2>').appendTo(this.out);
    this.dots = $('<span></span>').appendTo(this.title);
    this.status = $('<p style="display: none"></p>').appendTo(this.out);
    this.output = $('<ul class="search"/>').appendTo(this.out);

    $('#search-progress').text(_('Preparing search...'));
    this.startPulse();

    // index already loaded, the browser was quick!
    if (this.hasIndex())
      this.query(query);
    else
      this.deferQuery(query);
  },

  /**
   * execute search (requires search index to be loaded)
   */
  query : function(query) {
    var i;
    var stopwords = ["\u0430","\u0431\u0435\u0437","\u0431\u043e\u043b\u0435\u0435","\u0431\u043e\u043b\u044c\u0448\u0435","\u0431\u0443\u0434\u0435\u0442","\u0431\u0443\u0434\u0442\u043e","\u0431\u044b","\u0431\u044b\u043b","\u0431\u044b\u043b\u0430","\u0431\u044b\u043b\u0438","\u0431\u044b\u043b\u043e","\u0431\u044b\u0442\u044c","\u0432","\u0432\u0430\u043c","\u0432\u0430\u0441","\u0432\u0434\u0440\u0443\u0433","\u0432\u0435\u0434\u044c","\u0432\u043e","\u0432\u043e\u0442","\u0432\u043f\u0440\u043e\u0447\u0435\u043c","\u0432\u0441\u0435","\u0432\u0441\u0435\u0433\u0434\u0430","\u0432\u0441\u0435\u0433\u043e","\u0432\u0441\u0435\u0445","\u0432\u0441\u044e","\u0432\u044b","\u0433\u0434\u0435","\u0433\u043e\u0432\u043e\u0440\u0438\u043b","\u0434\u0430","\u0434\u0430\u0436\u0435","\u0434\u0432\u0430","\u0434\u043b\u044f","\u0434\u043e","\u0434\u0440\u0443\u0433\u043e\u0439","\u0435\u0433\u043e","\u0435\u0435","\u0435\u0439","\u0435\u043c\u0443","\u0435\u0441\u043b\u0438","\u0435\u0441\u0442\u044c","\u0435\u0449\u0435","\u0436","\u0436\u0435","\u0436\u0438\u0437\u043d\u044c","\u0437\u0430","\u0437\u0430\u0447\u0435\u043c","\u0437\u0434\u0435\u0441\u044c","\u0438","\u0438\u0437","\u0438\u043b\u0438","\u0438\u043c","\u0438\u043d\u043e\u0433\u0434\u0430","\u0438\u0445","\u043a","\u043a\u0430\u0436\u0435\u0442\u0441\u044f","\u043a\u0430\u043a","\u043a\u0430\u043a\u0430\u044f","\u043a\u0430\u043a\u043e\u0439","\u043a\u043e\u0433\u0434\u0430","\u043a\u043e\u043d\u0435\u0447\u043d\u043e","\u043a\u0442\u043e","\u043a\u0443\u0434\u0430","\u043b\u0438","\u043b\u0443\u0447\u0448\u0435","\u043c\u0435\u0436\u0434\u0443","\u043c\u0435\u043d\u044f","\u043c\u043d\u0435","\u043c\u043d\u043e\u0433\u043e","\u043c\u043e\u0436\u0435\u0442","\u043c\u043e\u0436\u043d\u043e","\u043c\u043e\u0439","\u043c\u043e\u044f","\u043c\u044b","\u043d\u0430","\u043d\u0430\u0434","\u043d\u0430\u0434\u043e","\u043d\u0430\u043a\u043e\u043d\u0435\u0446","\u043d\u0430\u0441","\u043d\u0435","\u043d\u0435\u0433\u043e","\u043d\u0435\u0435","\u043d\u0435\u0439","\u043d\u0435\u043b\u044c\u0437\u044f","\u043d\u0435\u0442","\u043d\u0438","\u043d\u0438\u0431\u0443\u0434\u044c","\u043d\u0438\u043a\u043e\u0433\u0434\u0430","\u043d\u0438\u043c","\u043d\u0438\u0445","\u043d\u0438\u0447\u0435\u0433\u043e","\u043d\u043e","\u043d\u0443","\u043e","\u043e\u0431","\u043e\u0434\u0438\u043d","\u043e\u043d","\u043e\u043d\u0430","\u043e\u043d\u0438","\u043e\u043f\u044f\u0442\u044c","\u043e\u0442","\u043f\u0435\u0440\u0435\u0434","\u043f\u043e","\u043f\u043e\u0434","\u043f\u043e\u0441\u043b\u0435","\u043f\u043e\u0442\u043e\u043c","\u043f\u043e\u0442\u043e\u043c\u0443","\u043f\u043e\u0447\u0442\u0438","\u043f\u0440\u0438","\u043f\u0440\u043e","\u0440\u0430\u0437","\u0440\u0430\u0437\u0432\u0435","\u0441","\u0441\u0430\u043c","\u0441\u0432\u043e\u044e","\u0441\u0435\u0431\u0435","\u0441\u0435\u0431\u044f","\u0441\u0435\u0433\u043e\u0434\u043d\u044f","\u0441\u0435\u0439\u0447\u0430\u0441","\u0441\u043a\u0430\u0437\u0430\u043b","\u0441\u043a\u0430\u0437\u0430\u043b\u0430","\u0441\u043a\u0430\u0437\u0430\u0442\u044c","\u0441\u043e","\u0441\u043e\u0432\u0441\u0435\u043c","\u0442\u0430\u043a","\u0442\u0430\u043a\u043e\u0439","\u0442\u0430\u043c","\u0442\u0435\u0431\u044f","\u0442\u0435\u043c","\u0442\u0435\u043f\u0435\u0440\u044c","\u0442\u043e","\u0442\u043e\u0433\u0434\u0430","\u0442\u043e\u0433\u043e","\u0442\u043e\u0436\u0435","\u0442\u043e\u043b\u044c\u043a\u043e","\u0442\u043e\u043c","\u0442\u043e\u0442","\u0442\u0440\u0438","\u0442\u0443\u0442","\u0442\u044b","\u0443","\u0443\u0436","\u0443\u0436\u0435","\u0445\u043e\u0440\u043e\u0448\u043e","\u0445\u043e\u0442\u044c","\u0447\u0435\u0433\u043e","\u0447\u0435\u043b\u043e\u0432\u0435\u043a","\u0447\u0435\u043c","\u0447\u0435\u0440\u0435\u0437","\u0447\u0442\u043e","\u0447\u0442\u043e\u0431","\u0447\u0442\u043e\u0431\u044b","\u0447\u0443\u0442\u044c","\u044d\u0442\u0438","\u044d\u0442\u043e\u0433\u043e","\u044d\u0442\u043e\u0439","\u044d\u0442\u043e\u043c","\u044d\u0442\u043e\u0442","\u044d\u0442\u0443","\u044f"];

    // stem the searchterms and add them to the correct list
    var stemmer = new Stemmer();
    var searchterms = [];
    var excluded = [];
    var hlterms = [];
    var tmp = query.split(/\s+/);
    var objectterms = [];
    for (i = 0; i < tmp.length; i++) {
      if (tmp[i] !== "") {
          objectterms.push(tmp[i].toLowerCase());
      }

      if ($u.indexOf(stopwords, tmp[i].toLowerCase()) != -1 || tmp[i].match(/^\d+$/) ||
          tmp[i] === "") {
        // skip this "word"
        continue;
      }
      // stem the word
      var word = stemmer.stemWord(tmp[i].toLowerCase());
      var toAppend;
      // select the correct list
      if (word[0] == '-') {
        toAppend = excluded;
        word = word.substr(1);
      }
      else {
        toAppend = searchterms;
        hlterms.push(tmp[i].toLowerCase());
      }
      // only add if not already in the list
      if (!$u.contains(toAppend, word))
        toAppend.push(word);
    }
    var highlightstring = '?highlight=' + $.urlencode(hlterms.join(" "));

    // console.debug('SEARCH: searching for:');
    // console.info('required: ', searchterms);
    // console.info('excluded: ', excluded);

    // prepare search
    var terms = this._index.terms;
    var titleterms = this._index.titleterms;

    // array of [filename, title, anchor, descr, score]
    var results = [];
    $('#search-progress').empty();

    // lookup as object
    for (i = 0; i < objectterms.length; i++) {
      var others = [].concat(objectterms.slice(0, i),
                             objectterms.slice(i+1, objectterms.length));
      results = results.concat(this.performObjectSearch(objectterms[i], others));
    }

    // lookup as search terms in fulltext
    results = results.concat(this.performTermsSearch(searchterms, excluded, terms, titleterms));

    // let the scorer override scores with a custom scoring function
    if (Scorer.score) {
      for (i = 0; i < results.length; i++)
        results[i][4] = Scorer.score(results[i]);
    }

    // now sort the results by score (in opposite order of appearance, since the
    // display function below uses pop() to retrieve items) and then
    // alphabetically
    results.sort(function(a, b) {
      var left = a[4];
      var right = b[4];
      if (left > right) {
        return 1;
      } else if (left < right) {
        return -1;
      } else {
        // same score: sort alphabetically
        left = a[1].toLowerCase();
        right = b[1].toLowerCase();
        return (left > right) ? -1 : ((left < right) ? 1 : 0);
      }
    });

    // for debugging
    //Search.lastresults = results.slice();  // a copy
    //console.info('search results:', Search.lastresults);

    // print the results
    var resultCount = results.length;
    function displayNextItem() {
      // results left, load the summary and display it
      if (results.length) {
        var item = results.pop();
        var listItem = $('<li style="display:none"></li>');
        if (DOCUMENTATION_OPTIONS.FILE_SUFFIX === '') {
          // dirhtml builder
          var dirname = item[0] + '/';
          if (dirname.match(/\/index\/$/)) {
            dirname = dirname.substring(0, dirname.length-6);
          } else if (dirname == 'index/') {
            dirname = '';
          }
          listItem.append($('<a/>').attr('href',
            DOCUMENTATION_OPTIONS.URL_ROOT + dirname +
            highlightstring + item[2]).html(item[1]));
        } else {
          // normal html builders
          listItem.append($('<a/>').attr('href',
            item[0] + DOCUMENTATION_OPTIONS.FILE_SUFFIX +
            highlightstring + item[2]).html(item[1]));
        }
        if (item[3]) {
          listItem.append($('<span> (' + item[3] + ')</span>'));
          Search.output.append(listItem);
          listItem.slideDown(5, function() {
            displayNextItem();
          });
        } else if (DOCUMENTATION_OPTIONS.HAS_SOURCE) {
          $.ajax({url: DOCUMENTATION_OPTIONS.URL_ROOT + '_sources/' + item[0] + '.txt',
                  dataType: "text",
                  complete: function(jqxhr, textstatus) {
                    var data = jqxhr.responseText;
                    if (data !== '' && data !== undefined) {
                      listItem.append(Search.makeSearchSummary(data, searchterms, hlterms));
                    }
                    Search.output.append(listItem);
                    listItem.slideDown(5, function() {
                      displayNextItem();
                    });
                  }});
        } else {
          // no source available, just display title
          Search.output.append(listItem);
          listItem.slideDown(5, function() {
            displayNextItem();
          });
        }
      }
      // search finished, update title and status message
      else {
        Search.stopPulse();
        Search.title.text(_('Search Results'));
        if (!resultCount)
          Search.status.text(_('Your search did not match any documents. Please make sure that all words are spelled correctly and that you\'ve selected enough categories.'));
        else
            Search.status.text(_('Search finished, found %s page(s) matching the search query.').replace('%s', resultCount));
        Search.status.fadeIn(500);
      }
    }
    displayNextItem();
  },

  /**
   * search for object names
   */
  performObjectSearch : function(object, otherterms) {
    var filenames = this._index.filenames;
    var objects = this._index.objects;
    var objnames = this._index.objnames;
    var titles = this._index.titles;

    var i;
    var results = [];

    for (var prefix in objects) {
      for (var name in objects[prefix]) {
        var fullname = (prefix ? prefix + '.' : '') + name;
        if (fullname.toLowerCase().indexOf(object) > -1) {
          var score = 0;
          var parts = fullname.split('.');
          // check for different match types: exact matches of full name or
          // "last name" (i.e. last dotted part)
          if (fullname == object || parts[parts.length - 1] == object) {
            score += Scorer.objNameMatch;
          // matches in last name
          } else if (parts[parts.length - 1].indexOf(object) > -1) {
            score += Scorer.objPartialMatch;
          }
          var match = objects[prefix][name];
          var objname = objnames[match[1]][2];
          var title = titles[match[0]];
          // If more than one term searched for, we require other words to be
          // found in the name/title/description
          if (otherterms.length > 0) {
            var haystack = (prefix + ' ' + name + ' ' +
                            objname + ' ' + title).toLowerCase();
            var allfound = true;
            for (i = 0; i < otherterms.length; i++) {
              if (haystack.indexOf(otherterms[i]) == -1) {
                allfound = false;
                break;
              }
            }
            if (!allfound) {
              continue;
            }
          }
          var descr = objname + _(', in ') + title;

          var anchor = match[3];
          if (anchor === '')
            anchor = fullname;
          else if (anchor == '-')
            anchor = objnames[match[1]][1] + '-' + fullname;
          // add custom score for some objects according to scorer
          if (Scorer.objPrio.hasOwnProperty(match[2])) {
            score += Scorer.objPrio[match[2]];
          } else {
            score += Scorer.objPrioDefault;
          }
          results.push([filenames[match[0]], fullname, '#'+anchor, descr, score]);
        }
      }
    }

    return results;
  },

  /**
   * search for full-text terms in the index
   */
  performTermsSearch : function(searchterms, excluded, terms, titleterms) {
    var filenames = this._index.filenames;
    var titles = this._index.titles;

    var i, j, file;
    var fileMap = {};
    var scoreMap = {};
    var results = [];

    // perform the search on the required terms
    for (i = 0; i < searchterms.length; i++) {
      var word = searchterms[i];
      var files = [];
      var _o = [
        {files: terms[word], score: Scorer.term},
        {files: titleterms[word], score: Scorer.title}
      ];

      // no match but word was a required one
      if ($u.every(_o, function(o){return o.files === undefined;})) {
        break;
      }
      // found search word in contents
      $u.each(_o, function(o) {
        var _files = o.files;
        if (_files === undefined)
          return

        if (_files.length === undefined)
          _files = [_files];
        files = files.concat(_files);

        // set score for the word in each file to Scorer.term
        for (j = 0; j < _files.length; j++) {
          file = _files[j];
          if (!(file in scoreMap))
            scoreMap[file] = {}
          scoreMap[file][word] = o.score;
        }
      });

      // create the mapping
      for (j = 0; j < files.length; j++) {
        file = files[j];
        if (file in fileMap)
          fileMap[file].push(word);
        else
          fileMap[file] = [word];
      }
    }

    // now check if the files don't contain excluded terms
    for (file in fileMap) {
      var valid = true;

      // check if all requirements are matched
      if (fileMap[file].length != searchterms.length)
          continue;

      // ensure that none of the excluded terms is in the search result
      for (i = 0; i < excluded.length; i++) {
        if (terms[excluded[i]] == file ||
            titleterms[excluded[i]] == file ||
            $u.contains(terms[excluded[i]] || [], file) ||
            $u.contains(titleterms[excluded[i]] || [], file)) {
          valid = false;
          break;
        }
      }

      // if we have still a valid result we can add it to the result list
      if (valid) {
        // select one (max) score for the file.
        // for better ranking, we should calculate ranking by using words statistics like basic tf-idf...
        var score = $u.max($u.map(fileMap[file], function(w){return scoreMap[file][w]}));
        results.push([filenames[file], titles[file], '', null, score]);
      }
    }
    return results;
  },

  /**
   * helper function to return a node containing the
   * search summary for a given text. keywords is a list
   * of stemmed words, hlwords is the list of normal, unstemmed
   * words. the first one is used to find the occurance, the
   * latter for highlighting it.
   */
  makeSearchSummary : function(text, keywords, hlwords) {
    var textLower = text.toLowerCase();
    var start = 0;
    $.each(keywords, function() {
      var i = textLower.indexOf(this.toLowerCase());
      if (i > -1)
        start = i;
    });
    start = Math.max(start - 120, 0);
    var excerpt = ((start > 0) ? '...' : '') +
      $.trim(text.substr(start, 240)) +
      ((start + 240 - text.length) ? '...' : '');
    var rv = $('<div class="context"></div>').text(excerpt);
    $.each(hlwords, function() {
      rv = rv.highlightText(this, 'highlighted');
    });
    return rv;
  }
};

$(document).ready(function() {
  Search.init();
});