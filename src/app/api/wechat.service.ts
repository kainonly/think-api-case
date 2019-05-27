import {Injectable} from '@angular/core';
import {HttpService} from 'ngx-bit-lite';
import {switchMap} from 'rxjs/operators';
import {Observable, of} from 'rxjs';

declare global {
  interface Window {
    wx: any;
  }
}

@Injectable()
export class WechatService {
  private elementScripts: HTMLElement;
  private isReady = false;

  constructor(private http: HttpService) {
  }

  /**
   * 懒加载微信JSSDK
   */
  loadScripts() {
    this.elementScripts = document.createElement('script');
    this.elementScripts.setAttribute('type', 'text/javascript');
    this.elementScripts.setAttribute('src', 'https://res.wx.qq.com/open/js/jweixin-1.4.0.js');
    document.body.appendChild(this.elementScripts);
  }

  /**
   * 加载 JSSDK
   */
  ready(): Observable<any> {
    return this.isReady ? of(window.wx) : this.http.req('wechat/jssdk').pipe(
      switchMap(res => {
        return res.error ? of(false) : Observable.create(observer => {
          window.wx.config(res.data);
          window.wx.ready(() => {
            this.isReady = true;
            observer.next(window.wx);
            observer.complete();
          });
        });
      })
    );
  }
}
