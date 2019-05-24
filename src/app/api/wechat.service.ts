import {Injectable} from '@angular/core';
import {HttpService} from 'ngx-bit-lite';

@Injectable()
export class WechatService {
  private elementScripts: HTMLElement;

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
  jssdk() {
    return this.http.req('wechat/jssdk');
  }
}
