webpackJsonp([1],{33:function(t,e,a){"use strict";var s=a(19),n=a(77),i=a(74),o=a.n(i);s.a.use(n.a),e.a=new n.a({routes:[{path:"/",name:"Search",component:o.a}]})},34:function(t,e,a){a(72);var s=a(32)(a(35),a(75),null,null);t.exports=s.exports},35:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default={name:"app"}},36:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=a(38),n=a.n(s);e.default={name:"Search",data:function(){return{isSearch:!1,results:[],maxResult:500,countEmails:0,skip:0,config:{apiKey:"",city:"",quest:"",count:10,getEmail:""}}},methods:{search:function(){var t=this;"all"!=(arguments.length>0&&void 0!==arguments[0]?arguments[0]:"all")&&(this.results.length=0),axios.get("https://search-maps.yandex.ru/v1/?text="+this.config.city+" "+this.config.quest+"&type=biz&lang=ru_RU&results="+("all"==this.config.count?this.maxResult:this.config.count)+"&apikey="+this.config.apiKey+"&skip="+this.skip).then(function(e){console.log("results"),console.log(e),t.isSearch=!0;var a=e.data.features.map(function(t){var e=t.properties.CompanyMetaData;return{name:e.name,url:e.url,phones:e.Phones?e.Phones.map(function(t){return t.formatted}).join(", "):"",adres:e.address,emails:""}});if(a.forEach(function(t){this.results.push(t)},t),t.skip+=t.maxResult,a.length<t.maxResult){Materialize.toast("Загрузка завершена. Найдено "+t.results.length+" записей."),Materialize.toast("Ищу email'ы.",1e4);var s=0,i=t.companyHasSite.map(function(e,a){return axios({method:"get",url:t.config.getEmail+e.url,crossDomain:!0}).then(function(i){e.emails=i.data.join(", "),s++,console.log("load email #"+a+" ("+s+"/"+t.companyHasSite.length+")"),n.a.resolve(""+e.emails)}).catch(function(t){n.a.resolve("Ошибка получения email`а: "+t)})});n.a.all(i).then(function(t){Materialize.toast("Загрузил все email`ы")}).catch(function(t){alert(t)}),t.skip=0}else t.search("all")}).catch(function(t){return Materialize.toast(t)})},save:function(){var t=new Blob(["\ufeff",this.resultCSV],{type:"application/json"});dlLink.href=URL.createObjectURL(t)}},mounted:function(){var t=this;axios.get("/static/config.json").then(function(e){console.log("load config"),t.config=e.data,t.$nextTick(function(){Materialize.updateTextFields()})}).catch(function(t){return Materialize.toast(t,1e4)})},computed:{resultCSV:function(){return"name; phone; adres; site; email\n"+this.fullDataCompany.join("\n")},fullDataCompany:function(){return this.results.map(function(t){return(t.name||"")+"; "+(t.phones||"")+"; "+(t.adres||"")+"; "+(t.url||"")+"; "+(t.emails||"")+" "})},countLoadEmails:function(){return this.results.filter(function(t){return""!=t.emails}).length},companyHasSite:function(){return this.results.filter(function(t){return t.url})}}}},37:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=a(19),n=a(34),i=a.n(n),o=a(33);s.a.config.productionTip=!1,new s.a({el:"#app",router:o.a,template:"<App/>",components:{App:i.a}})},72:function(t,e){},73:function(t,e){},74:function(t,e,a){a(73);var s=a(32)(a(36),a(76),"data-v-69045936",null);t.exports=s.exports},75:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{attrs:{id:"app"}},[a("router-view")],1)},staticRenderFns:[]}},76:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"row"},[a("div",{staticClass:"col s12"},[a("div",{staticClass:"card-panel"},[a("div",{staticClass:"row"},[a("div",{staticClass:"input-field col s10"},[a("input",{directives:[{name:"model",rawName:"v-model",value:t.config.quest,expression:"config.quest"}],attrs:{placeholder:"",id:"Quest",type:"text",autofocus:""},domProps:{value:t.config.quest},on:{keyup:function(e){if(!("button"in e)&&t._k(e.keyCode,"enter",13))return null;t.search(e)},input:function(e){e.target.composing||(t.config.quest=e.target.value)}}}),t._v(" "),a("label",{attrs:{for:"Quest"}},[t._v("Запрос")])]),t._v(" "),a("button",{staticClass:"waves-effect waves-light btn col s2",on:{click:t.search}},[t._v("Найти")])]),t._v(" "),0==t.results.length&t.isSearch?a("div",{staticClass:"center"},[a("p",[t._v("Ничего не найдено")])]):t._e(),t._v(" "),t._l(t.results,function(e,s){return a("div",{key:e,staticClass:"row"},[a("div",{staticClass:"col s1 truncate"},[t._v(t._s(s+1))]),t._v(" "),a("div",{staticClass:"col s3 truncate"},[t._v(t._s(e.name))]),t._v(" "),a("div",{staticClass:"col s2 truncate"},[t._v(t._s(e.phones))]),t._v(" "),a("div",{staticClass:"col s3 truncate"},[t._v(t._s(e.adres))]),t._v(" "),a("div",{staticClass:"col s2 truncate "},[t._v(t._s(e.url))]),t._v(" "),a("div",{staticClass:"col s1 truncate"},[t._v(t._s(e.emails))])])})],2)]),t._v(" "),a("div",{staticClass:"fixed-action-btn"},[a("a",{staticClass:"btn-floating btn-large waves-effect waves-light red",attrs:{id:"dlLink",download:"data.csv"},on:{click:t.save}},[a("i",{staticClass:"large material-icons"},[t._v("save")])])])])},staticRenderFns:[]}}},[37]);
//# sourceMappingURL=app.c41d913255ea5e38684a.js.map