import {Component} from '@angular/core';
import {WechatService} from '../../api/wechat.service';
import {BitService} from 'ngx-bit-lite';

@Component({
  selector: 'app-service',
  templateUrl: './service.component.html',
  styleUrls: ['./service.component.scss']
})
export class ServiceComponent {
  constructor(private wechatService: WechatService,
              public bit: BitService) {

  }

  loadSDK() {
    this.wechatService.ready().subscribe(data => {
      console.log(data);
    });
  }
}
