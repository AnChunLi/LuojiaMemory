import Qs from 'qs';
import Axios from 'axios';

// export let baseUrl = "http://localhost:9999/"
export let baseUrl = "https://ruobilin.1758541396.cn/Server/";
// function get(url,cbok,cberr){
//     Axios.get();
// }

export const post = function (url, args, cbok, cberr) {
    // 发送 POST 请求
    Axios({
        method: 'post',
        url: baseUrl + url,
        data: Qs.stringify(args)
    }).then(function (res) {
        console.log(res);
        cbok(res);
    }).catch(function (error) {
        console.log(error);
        cberr(error)
    });
}

export const get=function(url,cbok,cberr){
    Axios({
        method: 'get',
        url: baseUrl + url
    }).then(function (res) {
        console.log(res);
        cbok(res);
    }).catch(function (error) {
        console.log(error);
        cberr(error)
    });
}