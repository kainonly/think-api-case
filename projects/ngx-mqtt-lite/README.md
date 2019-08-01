# NgxMqttLite

A Lite Wrapper Around MQTT.js for Angular

[![npm](https://img.shields.io/npm/v/ngx-mqtt-lite.svg?style=flat-square)](https://www.npmjs.com/package/ngx-mqtt-lite)
[![Downloads](https://img.shields.io/npm/dm/ngx-mqtt-lite.svg?style=flat-square)](https://www.npmjs.com/package/ngx-mqtt-lite)
[![TypeScript](https://img.shields.io/badge/%3C%2F%3E-TypeScript-blue.svg?style=flat-square)](https://www.typescriptlang.org/)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://github.com/kainonly/ngx-message-queue/blob/master/LICENSE)

### Setup

```shell
npm install mqtt --save
npm install ngx-mqtt-lite --save
```

### Initialization

Add MQTT.js Library `angular.json`

```json
{
  "scripts": [
    "./node_modules/mqtt/dist/mqtt.min.js"
  ]
}
```

Edit `app.module`

```typescript
import {NgxMqttLiteService} from 'ngx-mqtt-lite';

@NgModule({
  providers: [
    NgxMqttLiteService,
  ],
})
export class AppModule {
}
```

Usage

```typescript
@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
})
export class AppComponent implements OnInit {
  constructor(private ngxMqttLiteService: NgxMqttLiteService) {

  }

  ngOnInit() {
    this.ngxMqttLiteService.initializa('ws://imac.com/ws', {
      username: 'kain',
      password: '123456',
      port: 15675,
      keepalive: 15
    });
    this.ngxMqttLiteService.scope().subscribe(client => {
      client.subscribe('erp.order.create');
    });
    this.ngxMqttLiteService.listen('message').subscribe(data => {
      console.log(data[1].toString());
    });
  }
}
```

### Api

- `client: MqttClient`
- `initializa(brokerUrl?: any, opts?: IClientOptions)` 
- `scope(): Observable<MqttClient>`
- `listen(event: string): Observable<any>`
- `destory()`
