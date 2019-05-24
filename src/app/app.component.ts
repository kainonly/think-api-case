import {Component, OnInit} from '@angular/core';
import {WechatService} from './api/wechat.service';

@Component({
  selector: 'app-root',
  template: '<router-outlet></router-outlet>',
})
export class AppComponent implements OnInit {
  constructor(private wechatService: WechatService) {
  }

  ngOnInit(): void {
    this.wechatService.loadScripts();
  }
}
