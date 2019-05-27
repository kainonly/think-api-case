import {Inject, Injectable, PLATFORM_ID} from '@angular/core';
import {isPlatformBrowser} from '@angular/common';
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
  private complete = false;
  ready: AsyncSubject<any> = new AsyncSubject();
  error: AsyncSubject<any> = new AsyncSubject();

  constructor(@Inject(PLATFORM_ID) private platformId: any,
              private http: HttpService) {
  }

  /**
   * Install Wechat Plugin
   */
  InstallPlugin(url: string) {
    if (isPlatformBrowser(this.platformId) && !this.complete) {
      this.loadScripts();
      this.http.req(url).pipe(
        switchMap(res => res.error ?
          throwError(res.msg) : of(res.data)
        ),
        retry(2)
      ).subscribe(config => {
        window.wx.config(config);
        window.wx.ready(() => {
          this.complete = true;
          this.ready.next(window.wx);
          this.ready.complete();
        });
        window.wx.error((res) => {
          this.error.next(res);
          this.error.complete();
        });
      });
    }
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
