import { get } from "../utils/httputil.js";

export const toLike=(id)=>{
    let list=JSON.parse(localStorage.getItem('list'));
    // console.log(list);
    let promise=new Promise(function(resolve,reject){
        for(let item of list){
            if(item.id==id){
                let url= item.isLiked==0?"release/addLike.php?id=" + id:"release/addLike.php?cancellike=1&id=" + id;
                get(
                    url,
                    res => {
                      if (res.data.code == 0) {
                        item.isLiked=item.isLiked==0?1:0;
                        localStorage.setItem('list',JSON.stringify(list));
                        resolve(item.isLiked);
                      }else{
                          reject();
                      }
                    },
                    err => {
                        reject();
                    }
                  );
            }
        }
    });
    return promise;
}
