import React from 'react';
import {
  Container, NavBar, Text, GradientBlock, IconButton, Carousel,
} from 'components';
import { StyleSheet, View, ScrollView } from 'react-native';
import { scale, verticalScale } from 'react-native-size-matters';
import { getCategories } from 'mocks/categories';
import PropTypes from 'prop-types';
import DailyDiscover from './Section/DailyDiscover';
import Categories from './Section/Categories';
import FlashSale from './Section/FlashSale';
import SearchBar from './Section/SearchBar';
import Popular from './Section/Popular';
import Brands from './Section/Brands';
import axios from 'axios';
import baseUrl from '../../../assets/common/baseUrl';

const styles = StyleSheet.create({
  card: {
    height: verticalScale(80),
    marginTop: verticalScale(-40),
    marginHorizontal: scale(20),
  },
  block: {
    paddingHorizontal: scale(14),
    paddingVertical: scale(10),
    flexDirection: 'row',
    alignItems: 'center',
  },
});

const Home = ({ navigation }) => {
  const [slider , setSlider] = React.useState([])
  axios
  .get(`${baseUrl}sliders`)
  .then((res)=>{
    setSlider(res.data.Data)
  })
  .catch((error)=>{
    console.log("something wrong")
  })
return(
  <Container>
    <NavBar
      variant="gradient"
      renderLeftComponent={() => (
        <SearchBar navigation={navigation} />
      )}
      renderRightComponent={() => (
        <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
          <IconButton
            color="white"
            icon="message-square"
            size={22}
            style={{ paddingLeft: scale(14) }}
            badge={2}
            onPress={() => navigation.navigate('Chat')}
          />
          <IconButton
            color="white"
            icon="bell"
            size={22}
            style={{ paddingLeft: scale(14) }}
            badge={8}
            onPress={() => navigation.navigate('Notification')}
          />
        </View>
      )}
    />
    <ScrollView bounces={false}>
      <GradientBlock style={styles.block}>
        <Text weight="medium" color="white">Popular: </Text>
        <Popular navigation={navigation} />
      </GradientBlock>
      <Carousel
        images={
          slider.map(i=>`https://ecpmarket.mywheels.pk/assets/images/sliders/${i.photo_mobile}`)
        }
      />
      <Categories
        navigation={navigation}
      />
      <FlashSale
        navigation={navigation}
      />
      <Brands
      navigation={navigation}
      />
      <DailyDiscover
        navigation={navigation}
      />
    </ScrollView>
  </Container>
)};

Home.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Home;
