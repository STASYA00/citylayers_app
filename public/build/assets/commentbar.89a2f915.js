var c=Object.defineProperty;var r=(s,e,t)=>e in s?c(s,e,{enumerable:!0,configurable:!0,writable:!0,value:t}):s[e]=t;var m=(s,e,t)=>(r(s,typeof e!="symbol"?e+"":e,t),t);class a extends CElement{constructor(t,n){super(t,"id");m(this,"toggleMarker",t=>{console.log(t)});this.name=CLASSNAMES.COMMENTPANEL,this.content=n,this.elements=[C,u,d]}getParent(){let t=document.getElementsByClassName(this.parent);if(t.length>0)return t[0]}load(t){this.elements.forEach(n=>{let i=new n(this.make_id(),"id");i.initiate(),i.load(t)})}static search(t){const n=document.getElementById(`${CLASSNAMES.COMMENTPANEL}_id`),i=document.getElementById(`${CLASSNAMES.COMMENTCONTAINER}_id`);n.classList.contains("open")||n.classList.add("open");let o=Array.from(document.getElementsByClassName(CLASSNAMES.COMMENTTEXT));o.forEach(l=>l.parentElement.setAttribute("style","order: 8")),o.filter(l=>l.innerHTML.toLowerCase().includes(t.toLowerCase())).forEach(l=>l.parentElement.setAttribute("style","order: 1")),i.scrollLeft=0}static focusComment(t,n){let i=document.getElementById(`commentpane_${t}`);console.log(i),i!=null&&n==!0&&(i.scrollIntoView({behavior:"smooth"}),i.focus())}static toggle(){document.getElementById(`${CLASSNAMES.COMMENTPANEL}_id`).classList.toggle("open")}static hideAll(){let t=document.getElementsByClassName(CLASSNAMES.CATEGORY_SIDE_PANEL);Array.from(t).forEach(n=>{n.style.display="none"})}}class d extends CElement{constructor(e,t){super(e),this.id=t,this.name=CLASSNAMES.COMMENTCONTAINER,this.elements=[]}addComment(e,t){let n=new h(this.make_id(),t,e);n.initiate(),n.load()}load(e){this.elements.forEach(t=>{let n=new t(this.make_id(),"main");n.initiate(),n.load()}),e.forEach((t,n)=>{this.addComment(t.comment,t.place_id.toString())})}}class h extends CElement{constructor(e,t,n){super(e),this.id=t,this.name=CLASSNAMES.COMMENTPANE,this.content=n,this.elements=[E]}initiate(){let e=document.createElement("div");e.setAttribute("class",this.name+" simple-drop-shadow"),e.setAttribute("id",this.make_id()),this.getParent().appendChild(e),e.setAttribute("tabindex","0"),e.onclick=()=>{a.toggleMarker(this.id,document.activeElement==e)}}load(){for(let e=0;e<this.elements.length;e++)new this.elements[e](this.make_id(),this.id,this.content).initiate()}}class E extends CElement{constructor(e,t,n){super(e),this.id=t,this.content=n,this.name=CLASSNAMES.COMMENTTEXT,this.parent=e}initiate(){let e=document.createElement("div");e.setAttribute("class",this.name),e.setAttribute("id",this.make_id()),e.innerHTML=this.content,this.getParent().appendChild(e)}}class C extends CElement{constructor(e){super(e,"id"),this.name=CLASSNAMES.COMMENTPANEL_CLOSE,this.content="<div class='chevron'></div>"}initiate(){let e=document.createElement("button");e.setAttribute("class",this.name),e.setAttribute("id",this.make_id()),e.innerHTML=this.content,e.onclick=t=>{e.classList.toggle("open"),a.toggle()},this.getParent().appendChild(e)}}class u extends CElement{constructor(e){super(e,"id"),this.name=CLASSNAMES.COMMENTSEARCH,this.content="Search through comments"}initiate(){let e=document.createElement("div");e.setAttribute("class",this.name),e.setAttribute("id",this.make_id()),this.getParent().appendChild(e);let t=document.createElement("input");t.setAttribute("type","text"),t.setAttribute("placeholder",this.content),t.oninput=n=>{a.search(n.target.value)},e.appendChild(t)}}
