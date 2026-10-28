// Pure route and fuel helpers. Road lengths always come from OSRM legs.
export const COUNTRIES = {
  US: {name:'United States',zone:'America/Chicago',cities:[['Austin, TX',-97.7431,30.2672],['Los Angeles, CA',-118.2437,34.0522]]},
  CA: {name:'Canada',zone:'America/Toronto',cities:[['Toronto',-79.3832,43.6532],['Ottawa',-75.6972,45.4215]]},
  DE: {name:'Germany',zone:'Europe/Berlin',cities:[['Berlin',13.405,52.52],['Hamburg',9.9937,53.5511]]},
  ES: {name:'Spain',zone:'Europe/Madrid',cities:[['Madrid',-3.7038,40.4168],['Valencia',-.3763,39.4699]]},
  PK: {name:'Pakistan',zone:'Asia/Karachi',cities:[['Lahore',74.3587,31.5204],['Islamabad',73.0479,33.6844]]},
  GB: {name:'United Kingdom',zone:'Europe/London',cities:[['London',-.1276,51.5072],['Birmingham',-1.8904,52.4862]]},
  FR: {name:'France',zone:'Europe/Paris',cities:[['Paris',2.3522,48.8566],['Lyon',4.8357,45.764]]},
  AU: {name:'Australia',zone:'Australia/Sydney',cities:[['Sydney',151.2093,-33.8688],['Canberra',149.13,-35.2809]]},
  AE: {name:'United Arab Emirates',zone:'Asia/Dubai',cities:[['Dubai',55.2708,25.2048],['Abu Dhabi',54.3773,24.4539]]},
  SA: {name:'Saudi Arabia',zone:'Asia/Riyadh',cities:[['Riyadh',46.6753,24.7136],['Dammam',50.0888,26.4207]]}
};
export function optimize(stations, distance, initial=50) {
  if (!Number.isFinite(distance) || distance<0 || !Number.isFinite(initial) || initial<0 || initial>50) throw Error('Invalid fuel or distance.');
  let previous=0;
  for(const s of stations) {
    if(!Number.isFinite(s.mile)||s.mile<previous||s.mile>distance||!Number.isFinite(+s.price)||+s.price<=0) throw Error('Invalid station sequence.');
    previous=s.mile;
  }
  const nodes=[...stations,{mile:distance,price:0,end:true}];
  let fuel=initial,position=0,total=0; const stops=[];
  for(let i=0;i<nodes.length;i++) {
    const node=nodes[i], needed=(node.mile-position)/10;
    if(needed>fuel+1e-6) throw Error('Not enough verified stations on this route for a 500-mile tank. Try a different trip; missing stations are never invented.');
    fuel=Math.max(0,fuel-needed);position=node.mile;
    if(node.end) break;
    const cheaper=nodes.slice(i+1).find(n=>n.mile-position<=500&&+n.price<+node.price);
    const target=cheaper?(cheaper.mile-position)/10:Math.min(50,(distance-position)/10);
    const buy=Math.max(0,target-fuel);
    if(buy>1e-6) {const cost=buy*+node.price;total+=cost;stops.push({...node,gallons:buy,cost,arrival:fuel});fuel+=buy;}
  }
  return {stops,total,consumed:distance/10,remaining:fuel};
}
export function candidatesAlongRoute(coords,distance,stations) {
  const sy=69, sx=69.172*Math.cos(coords.reduce((n,c)=>n+c[1],0)/coords.length*Math.PI/180);
  let length=0;const segments=[];
  for(let i=1;i<coords.length;i++) {
    const a=[coords[i-1][0]*sx,coords[i-1][1]*sy],b=[coords[i][0]*sx,coords[i][1]*sy];
    const dx=b[0]-a[0],dy=b[1]-a[1],len=Math.hypot(dx,dy);
    if(len) {segments.push({a,b,dx,dy,len,position:length});length+=len;}
  }
  if(!length) return [];
  const found=[];
  for(const s of stations) {
    const x=s.lon*sx,y=s.lat*sy;let best=Infinity,position=0;
    for(const g of segments) {
      if(x<Math.min(g.a[0],g.b[0])-2||x>Math.max(g.a[0],g.b[0])+2||y<Math.min(g.a[1],g.b[1])-2||y>Math.max(g.a[1],g.b[1])+2) continue;
      const t=Math.max(0,Math.min(1,((x-g.a[0])*g.dx+(y-g.a[1])*g.dy)/(g.len*g.len)));
      const offset=Math.hypot(x-g.a[0]-t*g.dx,y-g.a[1]-t*g.dy);
      if(offset<best){best=offset;position=g.position+t*g.len;}
    }
    const mile=position/length*distance;
    if(best<=2&&mile>.01&&mile<distance-.01) found.push({...s,mile});
  }
  found.sort((a,b)=>a.mile-b.mile||+a.price-+b.price);
  if(found.length<=90) return found;
  const bins=new Map(), chosen=new Map();
  for(const s of found){const k=Math.min(44,Math.floor(s.mile/Math.max(1,distance/45)));if(!bins.has(k)) bins.set(k,[]);bins.get(k).push(s);}
  for(const group of bins.values()) for(const s of [group.reduce((a,b)=>+a.price<+b.price?a:b),group[Math.floor(group.length/2)]]) chosen.set(s.id,s);
  return [...chosen.values()].sort((a,b)=>a.mile-b.mile);
}
export function insideGeometry(lon,lat,geometry) {
  function ringContains(ring) {let hit=false;for(let i=0,j=ring.length-1;i<ring.length;j=i++) {const [xi,yi]=ring[i],[xj,yj]=ring[j];if((yi>lat)!==(yj>lat)&&lon<(xj-xi)*(lat-yi)/(yj-yi)+xi) hit=!hit;}return hit;}
  const polygons=geometry.type==='Polygon'?[geometry.coordinates]:geometry.coordinates;
  return polygons.some(p=>ringContains(p[0])&&!p.slice(1).some(ringContains));
}
export function orderedStations(stations,route) {
  let mile=0;return stations.map((s,i)=>{mile+=route.legs[i].distance/1609.344;return {...s,mile};});
}
