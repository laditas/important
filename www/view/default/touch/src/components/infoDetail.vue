<template>
    <header id="infoHeader">
        <a class="back" href="javascript:window.history.back();"></a>
        资讯详情
    </header>
    <div class="infoDetailWrap" style="background: #ffffff;">
        <h3 class="infoDetailTitle">资讯详情 | <span id="time">{{content.input_time}}</span></h3>
        <h3 class="infoDetailTitle2"></h3>
        <h3 class="infoDetailTitle3">作者：<span id="admin">{{content.source}}</span></h3>
        <div id="infoDetailContent">{{{content.content}}}</div>
    </div>
    <footer id="footer">
        <ul>
            <li><a><i class="foot"></i><br>大客户专区</a></li>
            <li><a><i class="foot2"></i><br>物性表</a></li>
            <li><a v-link={name:'index'}><i class="foot3"></i><br>首页</a></li>
            <li><a class='footerOn' v-link={name:'infolist'}><i class="foot4"></i><br>资讯</a></li>
            <li><a v-link={name:'melogged'}><i class="foot5"></i><br>我</a></li>
        </ul>
    </footer>
    <div id="backTop" style="display: none">
        <img src="../../img/topArrow.png">
    </div>
</template>
<script>
	module.exports={
        el:"#app",
        data:function () {
            return {
                content:{}
            }
        },
        ready:function () {
            console.log(this.$route.query.id);
            this.$http.post('/mobi/mainPage/getArticleDetail',{id:this.$route.query.id}).then(function(res){
                console.log(res.json());
                this.$set('content',res.json().articleDetail);           	
            },function(){
            	
            });
        }
	}
</script>
