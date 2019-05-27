import {Injectable} from '@angular/core';
import {retry, switchMap} from 'rxjs/operators';
import {AsyncSubject, of, throwError} from 'rxjs';
import {HttpService} from '../base/http.service';
import {WechatInterface} from '../types/wechat.interface';

declare global {
  interface Window {
    wx: WechatInterface;
  }
}

@Injectable()
export class WechatService {
  private elementScripts: HTMLElement;
  ready: AsyncSubject<any> = new AsyncSubject();

  constructor(private http: HttpService) {
  }

  /**
   * Install Wechat Plugin
   */
  InstallPlugin(url: string) {
    this.loadScripts();
    this.http.req(url).pipe(
      switchMap(res => res.error ?
        throwError(res.msg) : of(res.data)
      ),
      retry(2)
    ).subscribe(config => {
      window.wx.config(config);
      window.wx.ready(() => {
        this.ready.next(window.wx);
        this.ready.complete();
      });
    });
  }

  /**
   * Lazy load script
   */
  private loadScripts() {
    this.elementScripts = document.createElement('script');
    this.elementScripts.setAttribute('type', 'text/javascript');
    this.elementScripts.setAttribute('src', 'https://res.wx.qq.com/open/js/jweixin-1.4.0.js');
    document.body.appendChild(this.elementScripts);
  }
}
